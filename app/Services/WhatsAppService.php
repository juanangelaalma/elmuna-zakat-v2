<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $phone, string $message): bool
    {
        $baseUrl  = config('services.chatery.base_url');
        $apiKey   = config('services.chatery.api_key');
        $sessionId = config('services.chatery.session_id');

        if (!$sessionId) {
            Log::warning('WhatsAppService: CHATERY_SESSION_ID belum dikonfigurasi.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'X-Api-Key' => $apiKey,
            ])->post("{$baseUrl}/api/whatsapp/chats/send-text", [
                "sessionId" => $sessionId,
                "chatId" => $phone,
                "message" => $message,
                "typingTime" => 2000,
            ]);

            if (!$response->successful()) {
                Log::error('WhatsAppService: Gagal mengirim pesan WhatsApp.', [
                    'phone'    => $phone,
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WhatsAppService: Eksepsi saat mengirim pesan WhatsApp.', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
