<?php
// Prevent any output before headers
ob_start();

// Enable error reporting but don't display errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Set content type to JSON
header('Content-Type: application/json');

// Function to send JSON response
function sendJsonResponse($data) {
    ob_clean(); // Clear any output buffer
    echo json_encode($data);
    exit();
}

// Function to handle errors
function handleError($message) {
    sendJsonResponse(['error' => $message]);
}

// Get the request body
$input = file_get_contents('php://input');
if (!$input || strlen(trim($input)) === 0) {
    handleError('No input received');
}


// Parse JSON input
$inData = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    handleError('Invalid JSON input: ' . json_last_error_msg());
}

// Validate required fields
$requiredFields = ['UserID', 'FirstName', 'LastName', 'Email', 'Phone', 'Company'];
foreach ($requiredFields as $field) {
    if (!isset($inData[$field]) || empty($inData[$field])) {
        handleError("Missing required field: $field");
    }
}

// Extract data
$userID = $inData["UserID"];
$FirstName = $inData["FirstName"];
$LastName = $inData["LastName"];
$Email = $inData["Email"];
$Phone = $inData["Phone"];
$Company = $inData["Company"];

// Connect to database
try {
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    
    if ($conn->connect_error) {
        handleError("Database connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the insert
    $stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID, Company) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        handleError("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $FirstName, $LastName, $Phone, $Email, $userID, $Company);
    
    if (!$stmt->execute()) {
        handleError("Execute failed: " . $stmt->error);
    }

    // Success
    $stmt->close();
    $conn->close();
    sendJsonResponse(['error' => '']);

} catch (Exception $e) {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    handleError("Error: " . $e->getMessage());
}
?>
