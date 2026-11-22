<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected string $phoneNumberId;
    protected string $accessToken;
    protected string $businessPhone;

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID'));
        $this->accessToken = config('services.whatsapp.access_token', env('WHATSAPP_ACCESS_TOKEN'));
        $this->businessPhone = config('services.whatsapp.business_phone', env('WHATSAPP_BUSINESS_PHONE'));
    }

    public function sendText(string $to, string $message): bool
    {
        if (!$this->phoneNumberId || !$this->accessToken) {
            return false;
        }

        $to = preg_replace('/[^0-9]/', '', $to);

        $response = Http::withToken($this->accessToken)
            ->post("https://graph.facebook.com/v20.0/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['preview_url' => false, 'body' => $message],
            ]);

        return $response->successful();
    }
}
