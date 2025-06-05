<?php
// Start the session if needed
session_start();

// === MySQL Connection ===
$host = 'localhost';  
$db = 'COP4331';
$user = 'root';
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === (Optional) Show databases for debugging ===
// You can remove this after verifying connection works
$result = $conn->query("SHOW DATABASES");
if ($result) {
    echo "<h3>Databases accessible:</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Database'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Query failed: " . $conn->error;
}

// === Handle POST Request ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Form submitted with data: " . print_r($_POST, true));

    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $companyName = $_POST['companyName'];

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO Users (name, lastname, email, phonenumber, company) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $lastname, $email, $phonenumber, $companyName);

        if ($stmt->execute()) {
            echo "<script>alert('Contact saved successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html>
<head>
  <title>Contact Manager</title>
  <link rel="stylesheet" href="css/createPage.css">
</head>
<body>

<?php include 'navbar.php'; ?>


  <div class="my-clients-container">
    <h2>New contact</h2>
    <form action="" method="post">
      <div class="form-row">
        <input type="text" name="name" placeholder=" " id="nameInput">
        <label for="nameInput">Name</label>
      </div>
      <div class="form-row">
        <input type="text" name="lastname" placeholder=" " id="lastnameInput">
        <label for="lastnameInput">Last Name</label>
      </div>
      <div class="form-row">
        <input type="text" name="email" placeholder=" " id="emailInput">
        <label for="emailInput">Email Address</label>
      </div>
      <div class="form-row">
        <input type="text" name="phonenumber" placeholder=" " id="phoneInput">
        <label for="phoneInput">Phone Number</label>
      </div>
      <div class="form-row">
        <input type="text" name="companyName" placeholder=" " id="companyInput">
        <label for="companyInput">Company Name</label>
      </div>
      <div>
        <button type="submit">Submit</button>
      </div>
  </form>
  </div>

</body>
</html>