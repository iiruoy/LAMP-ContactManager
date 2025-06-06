<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("Raw Input: " . file_get_contents("php://input")); // debug
file_put_contents("log.txt", "Insert worked!\n", FILE_APPEND);

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    http_response_code(200); 
    exit(0);
}

// For actual POST requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$inData = getRequestInfo();

// Extract incoming data
$name = $inData["name"];
$lastname = $inData["lastname"];
$email = $inData["email"];
$phonenumber = $inData["phonenumber"];
$company = $inData["company"];

// Connect to the database
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    $stmt = $conn->prepare("INSERT INTO Users (name, lastname, email, phonenumber, company) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $lastname, $email, $phonenumber, $company);
    
    if ($stmt->execute()) {
        returnWithError(""); // success with empty error
    } else {
        returnWithError($stmt->error); // return error message
    }

    $stmt->close();
    $conn->close();
}

function getRequestInfo() {
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj) {
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err) {
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}
?>
