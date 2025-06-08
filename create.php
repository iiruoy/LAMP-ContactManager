<?php
// Start the session if needed
session_start();
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
  <form id="contactForm">
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

<script>
document.getElementById("contactForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const data = {
    UserID: sessionStorage.getItem("user_id"),// You'll need to get this from your login system
    FirstName: document.getElementById("nameInput").value,
    LastName: document.getElementById("lastnameInput").value,
    Email: document.getElementById("emailInput").value,
    Phone: document.getElementById("phoneInput").value,
    Company: document.getElementById("companyInput").value,
  };

  console.log("Sending data:", data);

  try {
    const response = await fetch("/proxy.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(data)
    });

    console.log("Response status:", response.status);
    const responseText = await response.text();
    console.log("Raw response:", responseText);

    let result;
    try {
      result = JSON.parse(responseText);
    } catch (e) {
      console.error("Failed to parse JSON:", e);
      throw new Error("Invalid JSON response from server");
    }

    if (result.error === "") {
      alert("Contact saved successfully!");
      location.reload();
    } else {
      alert("Error: " + result.error);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error saving contact: " + error.message);
  }
});
</script>

</body>
</html>
