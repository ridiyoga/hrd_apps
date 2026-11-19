<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BroadcastServices
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.mekari.client_id');
        $this->clientSecret = config('services.mekari.client_secret');
        $this->baseUrl = config('services.mekari.base_url');
    }

    private function generateHeaders(string $method, string $path): array
    {
        $datetime = gmdate("D, d M Y H:i:s") . " GMT";
        $requestLine = "{$method} {$path} HTTP/1.1";
        $payload = "date: {$datetime}\n{$requestLine}";

        $digest = hash_hmac('sha256', $payload, $this->clientSecret, true);
        $signature = base64_encode($digest);

        $authHeader = sprintf(
            'hmac username="%s", algorithm="hmac-sha256", headers="date request-line", signature="%s"',
            $this->clientId,
            $signature
        );
        // dd($authHeader, $signature);
        return [
            'Authorization' => $authHeader,
            'Date' => $datetime,
            'Content-Type' => 'application/json',
        ];
    }

    public function request(string $method, string $path, array $payload = [])
    {
        $headers = $this->generateHeaders($method, $path);

        $response = Http::withHeaders($headers)
            ->send($method, $this->baseUrl . $path, [
                'json' => $payload
            ]);

        return $response;
    }
}
