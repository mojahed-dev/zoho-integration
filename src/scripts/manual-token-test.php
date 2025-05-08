<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file from project root
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// $refresh_token = '1000.331421c3e12be606648f6c72e0d4dd67.1a5b79e1e3a01b8199e56598719cbd70';
// $client_id     = '1000.10TZ6PV9MP8RMJ9HPJ6E3AGY5HSVZI';
// $client_secret = '82af7dc59272a91591e89f5f00af1dc5ab4e65836c';

        $client_id = $_ENV['ZOHO_CLIENT_ID'] ?? null;;
        $client_secret = $_ENV['ZOHO_CLIENT_SECRET'] ?? null;
        $refresh_token = $_ENV['ZOHO_REFRESH_TOKEN'] ?? null;
        // $this->baseUrl = getenv('ZOHO_AUTH_URL');

        echo "Client ID: $client_id\n";
        echo "Secret: $client_secret\n";
        echo "Refresh Token: $refresh_token\n";

        // print_r($_ENV);




        
// Use the same one that works in Postman:
$base_url      = 'https://www.zohoapis.com'; // or .com if applicable


// echo "CLIENT ID: ". $client_id;
// $url = $base_url . '/oauth/v2/token';

$url = 'https://accounts.zoho.com/oauth/v2/token';


$postFields = http_build_query([
    'refresh_token' => $refresh_token,
    'client_id'     => $client_id,
    'client_secret' => $client_secret,
    'grant_type'    => 'refresh_token',
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false || $http_code !== 200) {
    echo "HTTP Code: $http_code\n";
    echo "cURL Error: " . curl_error($ch) . "\n";
    echo "Response: $response\n";
} else {
    echo "✅ Success:\n";
    echo $response;
}

curl_close($ch);
