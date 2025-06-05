<?php
session_start();

// MySQL connection
$host = 'localhost';
$db = 'COP4331';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_GET['name'] ?? '';
$contact = null;

// Handle form submission to update the contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['name'];
    $newLastName = $_POST['lastname'];
    $newEmail = $_POST['email'];
    $newPhone = $_POST['phonenumber'];

    $stmt = $conn->prepare("UPDATE Users SET name = ?, lastname = ?, email = ?, phonenumber = ? WHERE name = ?");
    $stmt->bind_param("sssss", $newName, $newLastName, $newEmail, $newPhone, $name);
    $stmt->execute();
    $stmt->close();

    // If name changed, update $name so we can reload the correct record
    $name = $newName;
}

// Fetch the updated user info from DB
$stmt = $conn->prepare("SELECT * FROM Users WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();
$stmt->close();

$company = $contact['company']; 

require_once 'config.php';
$url = "https://api.twelvedata.com/quote?symbol=". urlencode($company) . "&apikey=$apiKey";


$response = file_get_contents($url);
$data = json_decode($response, true);

$timeIntervalUrl = "https://api.twelvedata.com/time_series?symbol=".urlencode($company)."&interval=1day&outputsize=30&apikey=$apiKey";
$timeBarIntervalUrl = "https://api.twelvedata.com/time_series?symbol=".urlencode($company)."&interval=1day&outputsize=7&apikey=$apiKey";

$timeIntervaeralResponse = file_get_contents($timeIntervalUrl);
$timeData = json_decode($timeIntervaeralResponse, true);

$stockPrice = [];
$stockLabel = [];

if(!empty($timeData['values'])){ 
  foreach (array_reverse($timeData['values']) as $stockPoint){ 
    $stockLabel[] = $stockPoint['datetime'];
    $stockPrice[] = $stockPoint['close'];
  }
}

$timeBarIntervalUrlResponse = file_get_contents($timeBarIntervalUrl); 
$barTimeData = json_decode($timeBarIntervalUrlResponse, true);

$stockPriceBar = [];
$stockLabelBar = [];

if(!empty($barTimeData['values'])){ 
  foreach (array_reverse($barTimeData['values']) as $stockPointBar){ 
    $stockLabelBar[] = $stockPointBar['datetime'];
    $stockPriceBar[] = $stockPointBar['close'];
  }
}

echo "<pre>";
// print_r($data);
// print_r($timeData);

// print_r($contacts);
echo "</pre>";
$price = $data['name'] ?? 'N/A'; 
?>

<!DOCTYPE html>
<html>
<head>
  <title>Contact Manager</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
  <h3>Stock Chart</h3>
  <div class="outer-box-container">
    <div class="box-container-edit-detail-page-right-side">
        <form class="edit-contact-form" action="" method="post">
          <div class="form-row">
            <input type="text" name="name" id="editName" placeholder=" " value="<?php echo htmlspecialchars($contact['name']); ?>">
            <label for="editName">First Name</label>
          </div>
          <div class="form-row">
            <input type="text" name="lastname" id="editLastName" placeholder=" " value="<?php echo htmlspecialchars($contact['lastname']); ?>">
            <label for="editLastName">Last Name</label>
          </div>
          <div class="form-row">
            <input type="email" name="email" id="editEmail" placeholder=" " value="<?php echo htmlspecialchars($contact['email']); ?>">
            <label for="editEmail">Email</label>
          </div>
          <div class="form-row">
            <input type="text" name="phonenumber" id="editPhone" placeholder=" " value="<?php echo htmlspecialchars($contact['phonenumber']); ?>">
            <label for="editPhone">Phone Number</label>
          </div>
          <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
    
    <div class="box-container-chart-left-side">
        <canvas id="mySecondChart" width="100" height="60"></canvas>
        <div class="chart-container">
        <script>
          const barLabels = <?php echo json_encode($stockLabelBar); ?>;
          const barPrices = <?php echo json_encode($stockPriceBar); ?>;
  
          const ctx2 = document.getElementById('mySecondChart').getContext('2d');
  
          new Chart(ctx2, {
            type: 'bar',
            data: {
              labels: barLabels,
              datasets: [{
                label: '7 Day Closing Price',
                data: barPrices,
                borderColor: 'rgb(149, 173, 255)',
                borderWidth: 2,
                fill: true, 
                barThickness: 7,
                borderRadius: 8,
              
              }]
            },
            options: {
    scales: {
      x: {
        ticks: {
          display: false // Hide x-axis numbers
        },
        grid: {
          display: false // Optional: hide x-axis grid lines
        }
      },
      y: {
        ticks: {
          display: true // Hide y-axis numbers
        },
        grid: {
          display: false // Optional: hide y-axis grid lines
        }
      }
    }
  }
});
        </script>
      </div>
    </div>
  </div>
  <h3>Stock Chart</h3>
  <div class="chart-container">
        <canvas id="myChart" width="580" height="250"></canvas>
        <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
        <script>
          const labels = <?php echo json_encode($stockLabel); ?>;
          const prices = <?php echo json_encode($stockPrice); ?>;
  
          const ctx = document.getElementById('myChart').getContext('2d');
  
          new Chart(ctx, {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: '30 Day Closing Price',
      data: prices,
      borderColor: 'rgb(33, 85, 254)',
      borderWidth: 2,
      fill: true, 
    }]
  },
  options: {
    scales: {
      x: {
        ticks: {
          display: false // Hide x-axis numbers
        },
        grid: {
          display: false // Optional: hide x-axis grid lines
        }
      },
      y: {
        ticks: {
          display: true // Hide y-axis numbers
        },
        grid: {
          display: false // Optional: hide y-axis grid lines
        }
      }
    }
  }
});

        </script>
        <div class="scroll-container">
          <div class="content-from-api-container">
          <h1>hello</h1>
          <h1>hello</h1>
          <h1>hello</h1>
        </div>
        <div class="content-from-api-container">
          <h1>hello</h1>
          <h1>hello</h1>
          <h1>hello</h1>
        </div>
        <div class="content-from-api-container">
          <h1>hello</h1>
          <h1>hello</h1>
          <h1>hello</h1>
        </div>
        <div class="content-from-api-container">
          <h1>hello</h1>
          <h1>hello</h1>
          <h1>hello</h1>
        </div>
        <div class="content-from-api-container">
          <h1>hello</h1>
          <h1>hello</h1>
          <h1>hello</h1>
        </div>
        </div>
        
    </div>
  

</body>
</html>