<?php

namespace App\Services;

use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    protected string $token;

    public function __construct(?string $token = null)
    {
        $this->token = $token ?? (string) config('services.fonnte.token', env('FONNTE_TOKEN', ''));
    }

    /**
     * Send a WhatsApp message using Fonnte API and log the result.
     */
    public function send(string $phone, string $message): bool
    {
        $status = 'gagal';

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(15)->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            $jsonStatus = $response->json('status');
            $isSuccess = $response->successful() && ($jsonStatus === true || $jsonStatus === 'true' || $jsonStatus === 1 || $jsonStatus === null);

            $status = $isSuccess ? 'terkirim' : 'gagal';
        } catch (Throwable $e) {
            Log::error('Fonnte send error: '.$e->getMessage());
            $status = 'gagal';
        }

        WhatsappLog::create([
            'phone' => $phone,
            'direction' => 'keluar',
            'message' => $message,
            'message_type' => 'text',
            'status' => $status,
            'sent_at' => now(),
        ]);

        return $status === 'terkirim';
    }
}
