<?php
// Enable error reporting
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:8000"); // Adjust if needed
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set content type
header("Content-Type: application/json");

// Determine which endpoint to hit
$method = $_SERVER['REQUEST_METHOD'];
$requestBody = file_get_contents('php://input');

switch ($method) {
    case 'GET':
        $url = "http://159.223.115.226/LAMPAPI/getcontacts.php";
        break;
    case 'POST':
        $decodedInput = json_decode($requestBody, true);
        
        if (isset($decodedInput['login']) && isset($decodedInput['password']) && count($decodedInput) === 2) {
            // This is a login request
            $url = "http://159.223.115.226/LAMP-ContactManager/LAMPAPI/login.php";
        } elseif (isset($decodedInput['login']) && isset($decodedInput['password'])) {
            // This is a signup request
            $url = "http://159.223.115.226/LAMPAPI/register.php";
        } elseif (isset($decodedInput['search'])) {
            $url = "http://159.223.115.226/LAMPAPI/searchFirstLast.php";
        } elseif (isset($decodedInput['ID']) && isset($decodedInput['UserID'])) {
            $url = "http://159.223.115.226/LAMPAPI/deletecontact.php";
        } elseif (isset($decodedInput['ID'])) {
            $url = "http://159.223.115.226/LAMPAPI/updateContact.php";
        } else {
            $url = "http://159.223.115.226/LAMPAPI/addcontact.php";
        }
        break;


    default:
        echo json_encode(["error" => "Unsupported method: $method"]);
        exit();
}


error_log("Proxy forwarding $method request to: $url");
if (!empty($requestBody)) {
    error_log("Request body: $requestBody");
    $_SESSION['last_contact'] = $requestBody;
}

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

if ($method === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($requestBody)
    ]);
}

// Enable debug logging
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $verbose);

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

rewind($verbose);
$verboseLog = stream_get_contents($verbose);
error_log("CURL Debug Info: $verboseLog");
error_log("HTTP Status Code: $httpCode");
error_log("Response: $response");

if (curl_errno($ch)) {
    error_log("Curl error: " . curl_error($ch));
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    exit();
}
curl_close($ch);

// Check if response is HTML
if (strpos($response, '<html') !== false || strpos($response, '<!DOCTYPE') !== false) {
    echo json_encode([
        'error' => 'Server returned HTML instead of JSON',
        'details' => ['http_code' => $httpCode, 'preview' => substr($response, 0, 500)]
    ]);
    exit();
}

// Validate JSON
$decoded = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'error' => 'Invalid JSON response from server',
        'details' => [
            'json_error' => json_last_error_msg(),
            'http_code' => $httpCode,
            'preview' => substr($response, 0, 500)
        ]
    ]);
    exit();
}

// Success
echo $response;
?>
