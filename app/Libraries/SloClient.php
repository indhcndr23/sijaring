<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SloClient
{
    protected CURLRequest $client;
    protected RequestInterface $request;

    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('SLO_BASE_URL');

        if (!$this->baseUrl) {
            throw new \Exception("Base Url SLO belum didefinisikan", 500);
        }

        $this->client = service('curlrequest', [
            'http_errors' => false,
            'timeout'     => 5,
        ]);

        $this->request = service('request');
    }

    protected function getCookie(): string
    {
        return $this->request->getHeaderLine('cookie');
    }

    protected function request(string $method, string $uri): ResponseInterface
    {
        return $this->client->request($method, $this->baseUrl . $uri, [
            'headers' => [
                'Cookie'    => $this->getCookie(),
            ],
        ]);

    }

    public function me()
    {
        $response = $this->request('GET', '/auth/me');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Failed to fetch /auth/me", $response->getStatusCode());
        }

        return json_decode($response->getBody())->data;
    }

    public function layananInternal() {
        $response = $this->request('GET', '/layanan-internal');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Failed to fetch /layanan-internal", $response->getStatusCode());
        }

        return json_decode($response->getBody())->data;
    }
}
