<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Api\DeleteHandler;
use Api\CreateHandler;

$credentialsPath = __DIR__ . '/../../credentials.json';
$spreadsheetId = '16toobLSIjNM5Hx-CFR0sACal_RurIvdTUJw9VN64XtE';
$sheetName = 'Customer';

$scopes = ['https://www.googleapis.com/auth/spreadsheets.readonly'];
$credentials = new ServiceAccountCredentials($scopes, $credentialsPath);

$stack = HandlerStack::create();
$stack->push(new AuthTokenMiddleware($credentials));
$client = new GuzzleClient(['handler' => $stack, 'auth' => 'google_auth']);

// STEP 1: Fetch header row
$headerRange = "$sheetName!A1:AZ1";
$headerResponse = $client->get("https://sheets.googleapis.com/v4/spreadsheets/$spreadsheetId/values/$headerRange");
$headerData = json_decode((string) $headerResponse->getBody(), true);
$headers = $headerData['values'][0] ?? [];

$normalizedHeaders = array_map(fn($h) => strtolower(trim($h)), $headers);
$columnMap = array_flip($normalizedHeaders);

// Sanity check: ensure required columns exist
$requiredCols = ['emailid', 'display name'];
foreach ($requiredCols as $col) {
    if (!isset($columnMap[$col])) {
        echo "❌ Missing column in sheet header: $col\n";
        exit;
    }
}

// STEP 2: Fetch data rows
$dataRange = "$sheetName!A2:BA";
$dataResponse = $client->get("https://sheets.googleapis.com/v4/spreadsheets/$spreadsheetId/values/$dataRange");
$data = json_decode((string) $dataResponse->getBody(), true);
$rows = $data['values'] ?? [];

$createHandler = new CreateHandler();
// $deleteHandler = new DeleteHandler(); // if needed later

$seenSequenceNumbers = [];


foreach ($rows as $index => $row) {
    // $row = array_pad($row, count($headers), '');

    // If all values are empty → skip
    if (!array_filter($row)) {
        echo "⏭️ Skipping completely empty row $index\n";
        continue;
    }

    // Normalize the row to match header size
    $row = array_pad($row, count($headers), '');
    // var_dump($headersc);


    // Safe getter
    $get = fn($key) => isset($columnMap[$key]) ? ($row[$columnMap[$key]] ?? '') : '';

    //  Extract Fields

    $emailId        = trim($get('emailid'));
    $action         = strtolower(trim($get('action')));
    $customerName   = $get('customer name');
    $customerId     = $get('customer id');
    $planCode       = $get('plan code');
    $phone          = $get('phone');
    $mobilePhone    = $get('mobilephone');
    $currencyCode   = $get('currency code') ?: 'PHP';
    $payTerms       = $get('payment terms') ?: 0;
    $sequenceNumber = preg_replace('/[^a-zA-Z0-9]/', '', $get('cf.sequence number')); // sanitize


    // If email & action are BOTH missing → skip
    if ($emailId === '' && $action === '') {
        echo "⏭️ Skipping unused or extra row $index (email + action empty)\n";
        continue;
    }

    // Proceed only if email is valid
    // if (!filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
    //     echo "❌ Invalid email at row $index: [$emailId]\n";
    //     continue;
    // }

    // $sequenceNumber = $get('cf_sequence_number');
    $sequenceNumber = $get('cf.sequence number');

    // Remove spaces and make sure it’s alphanumeric
    $sequenceNumber = preg_replace('/[^a-zA-Z0-9]/', '', $sequenceNumber);

    // Skip if it's still empty or invalid
    // if (empty($sequenceNumber)) {
    //     echo "❌ Invalid Sequence Number after cleanup in row $index. Skipping.\n";
    //     continue;
    // }


    // echo "🔍 Row $index - Sequence Number: [" . $sequenceNumber . "]\n";


    // if (!$sequenceNumber) {
    //     echo "⚠️ No sequence number in row $index. Skipping.\n";
    //     continue;
    // }

    // Check if already seen
    // if (in_array($sequenceNumber, $seenSequenceNumbers)) {
    //     echo "⏭️ Duplicate Sequence Number [$sequenceNumber] found in row $index. Skipping.\n";
    //     continue;
    // }

    // $seenSequenceNumbers[] = $sequenceNumber;


    if ($action === 'add') {
        echo "➕ Creating new subscription...\n";

        // fallback for display name (optional)
        // $finalDisplayName = $displayName ?: trim("$firstName $lastName");
        // if (empty($finalDisplayName)) {
        //     $finalDisplayName = $customerName ?: "Unnamed Customer " . uniqid();
        // }

        // ✅ Construct payload correctly for subscriptions
        $payload = [
            "customer_id" => $customerId,
            "plan" => [
                "plan_code" => $planCode,
                "quantity" => 1,
                "item_custom_fields" => [
                    [
                        "label" => "cf_sequence_number",  // ✅ Must match the API field name
                        "value" => $sequenceNumber        // ✅ Must be alphanumeric (no spaces)
                    ]
                ]
            ],
            "currency_code" => $currencyCode ?: "PHP",
            "payment_terms" => (int) $payTerms,
            "payment_terms_label" => $payTerms ? "Due in $payTerms days" : "Due on Receipt",
            "auto_collect" => false
        ];

        echo "📦 Payload:\n" . json_encode($payload, JSON_PRETTY_PRINT) . "\n";

        // Uncomment below if you have a createSubscription handler ready
        // try {
        //     $createHandler->createSubscription($payload);
        //     echo "✅ Subscription created for: {$finalDisplayName} (Row $index)\n";
        // } catch (Exception $e) {
        //     echo "❌ Failed to create subscription in row $index: " . $e->getMessage() . "\n";
        // }
    }
}
