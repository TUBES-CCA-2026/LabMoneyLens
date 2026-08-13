<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReceiptAnalysisService
{
    public function analyze(string $mimeType, string $base64Contents, string $type, string $apiKey)
    {
        $prompt = "Anda adalah asisten yang mengekstrak informasi dari struk kasir Indonesia. " .
            "Kembalikan hanya JSON valid dengan field berikut: tanggal, nominal, kategori, uraian, kuantiti. " .
            "Gunakan format tanggal YYYY-MM-DD. Untuk nominal dan kuantiti, kembalikan hanya angka tanpa titik atau koma. " .
            "Jika field tidak dapat dijelaskan, kembalikan string kosong untuk field tersebut.";

        $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64Contents,
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
            return ['error' => $errorMessage, 'status' => $response->status() ?: 500];
        }

        $body = $response->json();
        $text = data_get($body, 'candidates.0.content.parts.0.text', '');
        $parsed = $this->parseJsonText((string) $text);

        $parsed['tanggal'] = $this->normalizeDate($parsed['tanggal']);
        $parsed['nominal'] = $this->normalizeNominal($parsed['nominal']);
        $parsed['type'] = $type;

        return ['data' => $parsed];
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
