<?php

namespace Api;

use Auth\TokenManager;
use GuzzleHttp\Client;

class DeleteHandler
{
    private $tokenManager;
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->tokenManager = new TokenManager();
        $this->client = new Client();
        $this->baseUrl = 'https://www.zohoapis.com'; // adjust if your region is different
    }

    public function deleteCustomer($customerId)
    {
        $accessToken = $this->tokenManager->getAccessToken();

        $url = $this->baseUrl . "/billing/v1/customers/{$customerId}";

        $response = $this->client->request('DELETE', $url, [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken {$accessToken}",
                'Accept'        => 'application/json'
            ]
        ]);

        return $response->getStatusCode() === 200;
    }
}
