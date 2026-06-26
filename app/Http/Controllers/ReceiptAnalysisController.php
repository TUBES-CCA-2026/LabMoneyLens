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
        $mimeType = $image->getMimeType();

        $prompt = "Anda adalah asisten yang mengekstrak informasi dari struk kasir Indonesia. " .
            "Kembalikan hanya JSON valid dengan field berikut: tanggal, nominal, kategori, uraian. " .
            "Gunakan format tanggal YYYY-MM-DD. Untuk nominal, kembalikan hanya angka tanpa titik atau koma. " .
            "Jika field tidak dapat dijelaskan, kembalikan string kosong untuk field tersebut.";

        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            return response()->json([
                'error' => 'GEMINI_API_KEY belum dikonfigurasi di file .env Anda. Harap ikuti panduan sebelumnya untuk mendapatkan API Key dari Google AI Studio.'
            ], 500);
        }

        $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $contents,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ],
        ]);

        if (!$response->successful()) {
            $body = $response->json();
            Log::error('Receipt analysis failed', ['status' => $response->status(), 'body' => $body]);
            $errorMessage = data_get($body, 'error.message', 'Gagal menganalisis gambar.');
            return response()->json(['error' => $errorMessage], $response->status() ?: 500);
        }

        $body = $response->json();
        $text = data_get($body, 'candidates.0.content.parts.0.text', '');
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
