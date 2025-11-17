<?php

namespace Btab\Http;

use Illuminate\Support\Facades\Http;

class BtabClient
{
    protected string $base;
    protected string $key;

    public function __construct()
    {
        $this->base = rtrim(config('btab.api_url'), '/');
        $this->key  = config('btab.api_key');
    }

    protected function headers()
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->key,
        ];
    }

    public function fetchProducts(int $page = 1)
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(config('btab.timeout', 30))
            ->get("{$this->base}/products", ['page' => $page]);

        if (!$response->successful()) {
            throw new \Exception("API Error: " . $response->body());
        }

        return $response->json();
    }

    public function sendPurchase(array $payload)
    {
        return Http::withHeaders($this->headers())
            ->timeout(config('btab.timeout', 30))
            ->post("{$this->base}/purchases", $payload);
    }
}
