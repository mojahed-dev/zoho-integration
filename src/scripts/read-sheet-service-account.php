<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Api\DeleteHandler;

$credentialsPath = __DIR__ . '/../../credentials.json';
$spreadsheetId = '16toobLSIjNM5Hx-CFR0sACal_RurIvdTUJw9VN64XtE'; // Replace this with your actual Sheet ID
$sheetName = 'Customer'; // Change this if your sheet tab name is different

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


$columnMap = array_flip($headers); // e.g., 'ID' => 0, 'Email' => 3, etc.

// STEP 2: Fetch data rows
// $dataRange = "$sheetName";
$dataRange = "$sheetName!A2:BA";

$dataResponse = $client->get("https://sheets.googleapis.com/v4/spreadsheets/$spreadsheetId/values/$dataRange");
$data = json_decode((string) $dataResponse->getBody(), true);
$rows = $data['values'] ?? [];

$deleteHandler = new DeleteHandler(); // Make sure this is already working

// echo "🔍 Headers Detected from Sheet:\n";
// foreach ($headers as $i => $header) {
//     echo "[$i] => '" . $header . "'\n";
// }
// // exit;

// print_r($headers);

// $headers = $data[0];
// $rows = array_slice($data, 1);


foreach ($rows as $index => $row) {
    // Pad the row to ensure it has the same number of elements as headers
    $row = array_pad($row, count($headers), '');
    $createdTime     = $row[$columnMap['Created Time']]     ?? '';
    $lastModifiedTime   = $row[$columnMap['Last Modified Time']]   ?? '';
    $customerId  = $row[$columnMap['Customer ID']]  ?? '';
    $customerName  = $row[$columnMap['Customer Name']]  ?? '';
    $displayName  = $row[$columnMap['Display Name']]  ?? '';
    $companyName  = $row[$columnMap['Company Name']]  ?? '';
    $firstName  = $row[$columnMap['First Name']]  ?? '';
    $lastName  = $row[$columnMap['Last Name']]  ?? '';
    $emailId  = $row[$columnMap['EmailID']]  ?? '';
    $phone  = $row[$columnMap['Phone']]  ?? '';
    $mobilePhone  = $row[$columnMap['MobilePhone']]  ?? '';
    $skypeIdentity  = $row[$columnMap['Skype Identity']]  ?? '';
    $facebook  = $row[$columnMap['Facebook']]  ?? '';
    $twitter  = $row[$columnMap['Twitter']]  ?? '';
    $payTerms = $row[$columnMap['Payment Terms']]  ?? '';
    $currencyCode = $row[$columnMap['Currency Code']]  ?? '';
    $currencyCode = $row[$columnMap['Currency Code']]  ?? '';
    $notes = $row[$columnMap['Notes']]  ?? '';
    $website = $row[$columnMap['Website']]  ?? '';
    $contactType = $row[$columnMap['Contact Type']]  ?? '';
    $customerSubType = $row[$columnMap['Customer Sub Type']]  ?? '';
    $customerAddressId = $row[$columnMap['Customer Address ID']]  ?? '';
    $billingAttention = $row[$columnMap['Billing Attention']]  ?? '';
    $billingAddress = $row[$columnMap['Billing Address']]  ?? '';
    $billingStreet2 = $row[$columnMap['Billing Street2']]  ?? '';
    $billingCity = $row[$columnMap['Billing City']]  ?? '';
    $billingState = $row[$columnMap['Billing State']]  ?? '';
    $billingCountry = $row[$columnMap['Billing Country']]  ?? '';
    $billingCode = $row[$columnMap['Billing Code']]  ?? '';
    $billingFax = $row[$columnMap['Billing Fax']]  ?? '';
    $billingPhone = $row[$columnMap['Billing Phone']]  ?? '';
    $shippingAttention = $row[$columnMap['Shipping Attention']]  ?? '';
    $shippingAddress = $row[$columnMap['Shipping Address']]  ?? '';
    $status = $row[$columnMap['Status']]  ?? '';
    $createdBy = $row[$columnMap['Created By']]  ?? '';
    $action = $row[$columnMap['Action']]  ?? '';
    // $action = strtolower(trim($row[$columnMap['Action']] ?? ''));



    // if ($action === 'delete') {
    //     echo "Deleting: $id - $name ($email)\n";
    //     try {
    //         $success = $deleteHandler->deleteCustomer($id);
    //         echo $success ? "✅ Deleted: $id\n" : "❌ Failed to delete: $id\n";
    //     } catch (Exception $e) {
    //         echo "⚠️ Error deleting $id: " . $e->getMessage() . "\n";
    //     }
    // } elseif ($action === 'update') {
    //     echo "Updating: $id - $name ($email)\n";
    //     // $updateHandler->updateCustomer([...]); // Coming soon
    // }
   
    // echo "Row $index:\n";
    
        echo "🔹 Row $index: Customer ID = $customerId | Name = $firstName $lastName | Action = $action\n";
  
}

