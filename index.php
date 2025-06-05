<?php
// Start session if needed (for navbar or other use)
session_start();

// MySQL Connection
$host = 'localhost';
$db = 'COP4331';
$user = 'root';
$pass = ''; // or your actual password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
}

// Fetch all users
$result = $conn->query("SELECT * FROM Users ORDER BY id DESC");
$contacts = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>



<!DOCTYPE html>
<html>
<head>
  <title>Contact Manager</title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <?php include 'navbar.php'; ?>
  <div class="ticker-tape">
    <div class="ticker-track">
      <span class="arrow-up">AAPL 192.32 ▲</span>
      <span class="arrow-down">GOOG 2831.12 ▼</span>
      <span class="arrow-up">TSLA 712.45 ▲</span>
      <span class="arrow-down">AMZN 3342.88 ▼</span>
      <span class="arrow-up">MSFT 299.01 ▲</span>
      <span class="arrow-up">NVDA 402.12 ▲</span>
      <span class="arrow-up">META 467.25 ▲</span>
      <span class="arrow-down">INTC 130.47 ▼</span>
      <span class="arrow-up">NFLX 593.84 ▲</span>
      <span class="arrow-down">BABA 86.75 ▼</span>
      <span class="arrow-up">AMD 158.92 ▲</span>
      <span class="arrow-down">DIS 102.13 ▼</span>
      <span class="arrow-up">CRM 265.48 ▲</span>
      <span class="arrow-up">ORCL 121.33 ▲</span>
      <span class="arrow-down">PYPL 70.29 ▼</span>
      <span class="arrow-up">UBER 68.55 ▲</span>
    </div>
  </div>

  <h1>Welcome to Contact Manager</h1>
  <?php if (!empty($contacts)): ?>
    <ul class="index-box-container">
      <?php foreach ($contacts as $index => $contact): ?>
        <li class="contact-row-item">
          <span >
            <a href="details.php?name=<?php echo urlencode($contact['name']); ?>">
              <?php echo htmlspecialchars($contact['name']); ?>
            </a>
          </span>
          <strong><?php echo htmlspecialchars($contact['lastname']); ?></strong>
          <strong><?php echo htmlspecialchars($contact['email']); ?></strong>
          <strong><?php echo htmlspecialchars($contact['phonenumber']); ?></strong>
          <strong><?php echo htmlspecialchars($contact['company']); ?></strong>
          <form method="POST" action="index.php">
            <input type="hidden" name="delete_id" value="<?php echo $contact['id']; ?>">
            <button class="button-delete">Delete</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No contacts added yet.</p>
  <?php endif; ?>


</body>
</html>

