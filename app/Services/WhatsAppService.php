<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp message using a Gateway API
     */
    public static function sendMessage(?string $phone, string $message): bool
    {
        if (empty($phone)) return false;

        // Ensure phone starts with country code (62 for Indonesia)
        $phone = self::formatPhone($phone);

        /**
         * PENTING: Untuk integrasi asli, Anda perlu mendaftar ke provider 
         * WA Gateway (seperti Fonnte, Wablas, atau Twilio) dan memasukkan API Key di sini.
         * Contoh di bawah adalah simulasi menggunakan Log.
         */
        try {
            // SIMULASI: Mencatat ke log laravel (storage/logs/laravel.log)
            Log::info("=== WHATSAPP NOTIFICATION SIMULATION ===");
            Log::info("Target: {$phone}");
            Log::info("Message: {$message}");
            Log::info("========================================");

            // Contoh pemanggilan API (Uncomment jika sudah memiliki provider):
            /*
            $response = Http::withHeaders([
                'Authorization' => 'TOKEN_GATEWAY_ANDA'
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);
            return $response->successful();
            */

            return true;
        } catch (\Exception $e) {
            Log::error("WA SEND FAILED: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number to international standard (62...)
     */
    private static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
