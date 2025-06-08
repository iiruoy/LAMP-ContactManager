<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

if ($conn->connect_error) {
    sendJsonResponse("Connection failed: " . $conn->connect_error);
    exit();
}

// Get all contacts
$sql = "SELECT * FROM Contacts ORDER BY ID DESC";
$result = $conn->query($sql);

if ($result) {
    $contacts = array();
    while($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    sendJsonResponse("", $contacts); // No error
} else {
    sendJsonResponse("Error retrieving contacts: " . $conn->error);
}

$conn->close();

// Format: { "error": "...", "contacts": [...] }
function sendJsonResponse($errorMessage, $contacts = null) {
    $response = array("error" => $errorMessage);

    if ($contacts !== null) {
        $response["contacts"] = $contacts;
    }

    echo json_encode($response);
    exit();
}
?>
