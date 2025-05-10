<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Service\Sheets;

$spreadsheetId = '115S-YWC8BBiJgcEn-ZyKeaJq6gXDDYB7AJXjw1I4fmk';  // Replace with your real Sheet ID
$range = 'Sheet1!A2:D'; // Skip header row

$credentialsPath = __DIR__ . '/../../credentials.json'; // Path to your service account JSON
$scopes = ['https://www.googleapis.com/auth/spreadsheets.readonly'];

$client = new \Google\Client();
$client->setAuthConfig($credentialsPath);
$client->setScopes($scopes);

$service = new Sheets($client);
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues();

foreach ($rows as $row) {
    $id     = $row[0] ?? '';
    $fname   = $row[1] ?? '';
    $mname  = $row[2] ?? '';
    $lname  = $row[2] ?? '';
    $status = strtolower(trim($row[3] ?? ''));

    echo "[$status] ID: $id | First Name: $fname | Middle Name: $mname | Last Name: $lname\n";
}
