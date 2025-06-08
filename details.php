<?php
session_start();
$lastContact = $_SESSION['last_contact'] ?? null;
if ($lastContact) {
    $contact = json_decode($lastContact, true);
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Remote API base and user context
$apiBase = "http://159.223.115.226/LAMPAPI";
$userID = $contact['UserID'];

// Identify contact by ID and optional name
$id   = $_GET['id']   ?? '';
$name = $_GET['name'] ?? '';
$contact = null;

// 1) Fetch all contacts for this user via search API
$searchPayload = json_encode(["UserID" => $userID, "search" => ""]);
$ch = curl_init("$apiBase/searchFirstLast.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $searchPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$decodedPayload = json_decode($searchPayload, true);
$userIdValue = $decodedPayload['UserID'];
// echo "User ID is: " . $response;    // Extracts just the ID


$data = json_decode($response, true);
if (!empty($data['results'])) {
    // 2) Find the specific contact by ID
    foreach ($data['results'] as $result) {
        if ($result['ID'] == $id) {
            $contact = $result;
            break;
        }
    }
}

// 3) Handle update via API on form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Build payload matching API expectations
    $updatePayload = [
        "FirstName" => $_POST['FirstName'],
        "LastName"  => $_POST['LastName'],
        "Email"     => $_POST['Email'],
        "Phone"     => $_POST['Phone'],
        "Company"   => $_POST['Company'] ?? $contact['Company'],
        "UserID"    => $userID,
        "ID"        => $_POST['ID']
    ];

    // Call the update endpoint (ensure correct filename casing)
    $ch = curl_init("$apiBase/updatecontact.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatePayload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $updateResp = curl_exec($ch);
    curl_close($ch);

    // Redirect back to details view of this contact
    header("Location: details.php?id=" . urlencode($updatePayload['ID']) . "&name=" . urlencode($updatePayload['FirstName']));
    exit;
}

$company = $contact['Company'] ?? '';

require_once 'config.php';
$url = "https://api.twelvedata.com/quote?symbol=". urlencode($company) . "&apikey=$apiKey";
$response = file_get_contents($url);
$data = json_decode($response, true);

// Chart Data
$timeIntervalUrl = "https://api.twelvedata.com/time_series?symbol=" . urlencode($company) . "&interval=1day&outputsize=30&apikey=$apiKey";
$barIntervalUrl = "https://api.twelvedata.com/time_series?symbol=" . urlencode($company) . "&interval=1day&outputsize=7&apikey=$apiKey";

$timeData = json_decode(file_get_contents($timeIntervalUrl), true);
$barData = json_decode(file_get_contents($barIntervalUrl), true);

$stockPrice = $stockLabel = $stockPriceBar = $stockLabelBar = [];

if (!empty($timeData['values'])) {
    foreach (array_reverse($timeData['values']) as $point) {
        $stockLabel[] = $point['datetime'];
        $stockPrice[] = $point['close'];
    }
}

if (!empty($barData['values'])) {
    foreach (array_reverse($barData['values']) as $pointBar) {
        $stockLabelBar[] = $pointBar['datetime'];
        $stockPriceBar[] = $pointBar['close'];
    }
}

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
  <div class="edit-header">
    <h3 class="edit-h3-header">Profile Edit</h3>
    <h3 class="edit-h3-header">User Info</h3>
    <h3>Details Panel</h3>
    <h3 class="edit-h3-header-push-right">Next 7 days closing</h3>
  </div>
  
  <div class="outer-box-container">
    <div class="box-container-edit-detail-page-right-side">
        <form class="edit-contact-form" action="" method="post">
          <input type="hidden" name="ID" value="<?php echo htmlspecialchars($contact['ID']); ?>">
          <div class="form-row">
            <input type="text" name="FirstName" id="editName" placeholder=" " value="<?php echo htmlspecialchars($contact['FirstName']); ?>">
            <label for="editName">First Name</label>
          </div>
          <div class="form-row">
            <input type="text" name="LastName" id="editLastName" placeholder=" " value="<?php echo htmlspecialchars($contact['LastName']); ?>">
            <label for="editLastName">Last Name</label>
          </div>
          <div class="form-row">
            <input type="email" name="Email" id="editEmail" placeholder=" " value="<?php echo htmlspecialchars($contact['Email']); ?>">
            <label for="editEmail">Email</label>
          </div>
          <div class="form-row">
            <input type="text" name="Phone" id="editPhone" placeholder=" " value="<?php echo htmlspecialchars($contact['Phone']); ?>">
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
    <canvas id="myChart" width="960" height="250"></canvas>
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
    </div>
  

</body>
</html>