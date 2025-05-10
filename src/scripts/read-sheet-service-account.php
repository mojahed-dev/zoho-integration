<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Google\Auth\Middleware\AuthTokenMiddleware;

// Load service account credentials
$credentialsPath = __DIR__ . '/../../credentials.json';
$scopes = ['https://www.googleapis.com/auth/spreadsheets.readonly'];

$credentials = new ServiceAccountCredentials($scopes, $credentialsPath);

// Build authorized Guzzle client
$stack = HandlerStack::create();
$middleware = new AuthTokenMiddleware($credentials);
$stack->push($middleware);
$client = new GuzzleClient([
    'handler' => $stack,
    'auth' => 'google_auth'
]);

// Set your Google Sheet ID and range
$spreadsheetId = '115S-YWC8BBiJgcEn-ZyKeaJq6gXDDYB7AJXjw1I4fmk'; 
$range = 'Sheet1!A2:Z';

// Fetch sheet data using Sheets API v4 endpoint
$response = $client->get("https://sheets.googleapis.com/v4/spreadsheets/$spreadsheetId/values/$range");
$data = json_decode((string) $response->getBody(), true);

// Display rows
foreach ($data['values'] as $index => $row) {
    // echo "Row $index raw: ";
    // print_r($row);
    $id     = $row[0] ?? '';
    $fname   = $row[1] ?? '';
    $mname  = $row[2] ?? '';
    $lname  = $row[3] ?? '';
    $status = isset($row[4]) ? strtolower(trim($row[4])) : 'missing';
    if (!in_array($status, ['update', 'delete'])) {
         continue; // skip invalid or empty status rows
    }
    echo "[$status] ID: $id | First Name: $fname | Middle Name: $mname | Last Name: $lname\n";
}