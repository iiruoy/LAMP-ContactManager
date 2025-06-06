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
    name: document.getElementById("nameInput").value,
    lastname: document.getElementById("lastnameInput").value,
    email: document.getElementById("emailInput").value,
    phonenumber: document.getElementById("phoneInput").value,
    company: document.getElementById("companyInput").value,
  };

  const response = await fetch("http://159.223.115.226/LAMP-ContactManager/LAMPAPI/addcontact.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  });

  const result = await response.json();
  if (result.error === "") {
    alert("Contact saved successfully!");
    location.reload();
  } else {
    alert("Error: " + result.error);
  }
});
</script>

</body>
</html>
