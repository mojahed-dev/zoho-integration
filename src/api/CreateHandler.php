<?php

namespace Api;
use Auth\TokenManager;
use GuzzleHttp\Client;

class CreateHandler {
    
    private $token;

    public function __construct()
    {
        $tokenManager = new TokenManager();
        $this->token = $tokenManager->getAccessToken();
        // var_dump($this->token);
    }

    public function createCustomer(array $data): bool
    {
        $client = new Client();
        $response = $client->post('https://www.zohoapis.com/subscriptions/v1/customers', [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken {$this->token}",
                'Content-Type' => 'application/json'
            ],
            'json' => $data
        ]);

        $status = $response->getStatusCode();
        return $status === 201 || $status === 200;
    }

}