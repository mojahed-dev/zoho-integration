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
        $response = $client->post('https://www.zohoapis.com/billing/v1/customers', [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken {$this->token}",
                'Content-Type' => 'application/json'
            ],
            'json' => $data
        ]);

        $status = $response->getStatusCode();
        return $status === 201 || $status === 200;
    }

    public function checkIfCustomerExistsBySequence(string $sequenceNumber): ?array
    {
        $tokenManager = new \Auth\TokenManager();
        $accessToken = $tokenManager->getAccessToken();

        $client = new \GuzzleHttp\Client();
        $url = "https://www.zohoapis.com/billing/v1/customers?filter_by=cf_sequence_number.Equals($sequenceNumber)";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => "Zoho-oauthtoken $accessToken",
                    'Accept'        => 'application/json'
                ]
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (!empty($body['customers'])) {
                return $body['customers'][0]; // return the first matched customer
            }

            return null;

            } catch (\Exception $e) {
                echo "❌ Error checking existing customer: " . $e->getMessage() . "\n";
                return null;
            }
    }


}