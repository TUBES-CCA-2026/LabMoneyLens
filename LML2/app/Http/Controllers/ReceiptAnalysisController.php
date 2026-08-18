<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptAnalysisRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReceiptAnalysisController extends Controller
{
    public function parse(ReceiptAnalysisRequest $request)
    {
        $image = $request->file('receipt_image');
        $contents = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'error' => 'GEMINI_API_KEY belum dikonfigurasi di file .env Anda. Harap ikuti panduan sebelumnya untuk mendapatkan API Key dari Google AI Studio.'
            ], 500);
        }

        $service = app(\App\Services\ReceiptAnalysisService::class);
        $result = $service->analyze($mimeType, $contents, $request->input('type'), $apiKey);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status'] ?? 500);
        }

        return response()->json(['data' => $result['data']]);
    }

    protected function parseJsonText(string $text): array
    {
        if (empty(trim($text))) {
            return ['tanggal' => '', 'nominal' => '', 'kategori' => '', 'uraian' => '', 'kuantiti' => ''];
        }

        $json = $this->extractJsonObject($text);
        if ($json !== null) {
            return [
                'tanggal' => strval($json['tanggal'] ?? ''),
                'nominal' => strval($json['nominal'] ?? ''),
                'kategori' => strval($json['kategori'] ?? ''),
                'uraian' => strval($json['uraian'] ?? ''),
                'kuantiti' => strval($json['kuantiti'] ?? ''),
            ];
        }

        return ['tanggal' => '', 'nominal' => '', 'kategori' => '', 'uraian' => '', 'kuantiti' => ''];
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
