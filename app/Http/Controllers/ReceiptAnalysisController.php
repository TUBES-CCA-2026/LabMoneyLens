<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReceiptAnalysisController extends Controller
{
    public function parse(Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|max:5120',
            'type' => 'required|in:pemasukan,pengeluaran',
        ]);

        $image = $request->file('receipt_image');
        $contents = base64_encode(file_get_contents($image->getRealPath()));
        $dataUri = 'data:' . $image->getMimeType() . ';base64,' . $contents;

        $prompt = "Anda adalah asisten yang mengekstrak informasi dari struk kasir Indonesia. " .
            "Kembalikan hanya JSON valid dengan field berikut: tanggal, nominal, kategori, uraian. " .
            "Gunakan format tanggal YYYY-MM-DD. Untuk nominal, kembalikan hanya angka tanpa titik atau koma. " .
            "Jika field tidak dapat dijelaskan, kembalikan string kosong untuk field tersebut.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'input_text', 'text' => $prompt],
                        ['type' => 'input_image', 'image_url' => $dataUri],
                    ],
                ],
            ],
            'max_output_tokens' => 500,
        ]);

        if (!$response->successful()) {
            $body = $response->json();
            Log::error('Receipt analysis failed', ['status' => $response->status(), 'body' => $body]);
            $errorMessage = data_get($body, 'error.message', data_get($body, 'error', 'Gagal menganalisis gambar.'));
            return response()->json(['error' => $errorMessage], $response->status());
        }

        $body = $response->json();
        $text = data_get($body, 'output.0.content.0.text', data_get($body, 'output.0.content.0', ''));
        $parsed = $this->parseJsonText((string) $text);

        $parsed['tanggal'] = $this->normalizeDate($parsed['tanggal']);
        $parsed['nominal'] = $this->normalizeNominal($parsed['nominal']);
        $parsed['type'] = $request->input('type');

        return response()->json(['data' => $parsed]);
    }

    protected function parseJsonText(string $text): array
    {
        if (empty(trim($text))) {
            return ['tanggal' => '', 'nominal' => '', 'kategori' => '', 'uraian' => ''];
        }

        $json = $this->extractJsonObject($text);
        if ($json !== null) {
            return [
                'tanggal' => strval($json['tanggal'] ?? ''),
                'nominal' => strval($json['nominal'] ?? ''),
                'kategori' => strval($json['kategori'] ?? ''),
                'uraian' => strval($json['uraian'] ?? ''),
            ];
        }

        return ['tanggal' => '', 'nominal' => '', 'kategori' => '', 'uraian' => ''];
    }

    protected function extractJsonObject(string $text): ?array
    {
        if ($decoded = json_decode($text, true)) {
            return is_array($decoded) ? $decoded : null;
        }

        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    protected function normalizeDate(string $value): string
    {
        if (empty(trim($value))) {
            return '';
        }

        try {
            $date = Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Throwable $exception) {
            return '';
        }
    }

    protected function normalizeNominal(string $value): string
    {
        if (empty(trim($value))) {
            return '';
        }

        $normalized = preg_replace('/[^0-9]/', '', $value);
        return $normalized ?: '';
    }
}
