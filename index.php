<?php
session_start();
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

<ul class="index-box-container" id="contactsContainer">
  <li>Loading contacts...</li>
</ul>

<script>
  // Fetch all contacts
  async function fetchContacts() {
    const payload = {
      UserID: sessionStorage.getItem("user_id"),
      search: ""
    };

    try {
      const response = await fetch("/proxy.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
      });

      const text = await response.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch (err) {
        document.getElementById("contactsContainer").innerHTML = `<li>Invalid JSON from server</li>`;
        console.error("Raw response:", text);
        return;
      }

      if (data.error !== "") {
        document.getElementById("contactsContainer").innerHTML = `<li>Error: ${data.error}</li>`;
        return;
      }

      if (!data.results || data.results.length === 0) {
        document.getElementById("contactsContainer").innerHTML = `<li>No contacts found.</li>`;
        return;
      }

      const contactsHtml = data.results.map(contact => `
        <li class="contact-row-item">
          <span>
            <a href="details.php?id=${contact.ID}">
              ${contact.FirstName}
            </a>
          </span>
          <strong>${contact.LastName}</strong>
          <strong>${contact.Email}</strong>
          <strong>${contact.Phone}</strong>
          <strong>${contact.Company}</strong>
          <button class="button-delete" onclick="deleteContact(${contact.ID})">Delete</button>
        </li>
      `).join("");

      document.getElementById("contactsContainer").innerHTML = contactsHtml;

    } catch (error) {
      document.getElementById("contactsContainer").innerHTML = `<li>Fetch error: ${error.message}</li>`;
      console.error("Fetch error:", error);
    }
  }

  // Delete a contact
  async function deleteContact(contactId) {
    if (!confirm("Are you sure you want to delete this contact?")) return;

    const payload = {
      ID: contactId,
      UserID: sessionStorage.getItem("user_id")
    };

    try {
      const response = await fetch("/proxy.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
      });

      const result = await response.json();

      if (result.error === "") {
        alert("Contact deleted successfully.");
        fetchContacts(); // Refresh list
      } else {
        alert("Error deleting contact: " + result.error);
      }
    } catch (err) {
      console.error(err);
      alert("Failed to delete contact.");
    }
  }

  // Load contacts on page load
  fetchContacts();
</script>

</body>
</html>
