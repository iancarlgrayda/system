<?php
session_start();

// 🔒 Restrict access: only logged-in clients allowed
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "client") {
  header("Location: ../login.html");
  exit();
}

$username = $_SESSION["username"];
$role = $_SESSION["role"];

// ✅ Database connection (same as signup.php)
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "sd_cre8tive";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

// ✅ Handle password update (plain text)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["current"], $_POST["new"], $_POST["confirm"])) {
  $current = $_POST["current"];
  $new = $_POST["new"];
  $confirm = $_POST["confirm"];

  // Fetch user password from DB
  $stmt = $conn->prepare("SELECT password FROM client WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $dbPassword = $row["password"];

    if ($dbPassword === $current) {
      if ($new === $confirm) {
        // Update password (plain text)
        $update = $conn->prepare("UPDATE client SET password = ? WHERE username = ?");
        $update->bind_param("ss", $new, $username);
        if ($update->execute()) {
          echo "<script>alert('✅ Password updated successfully!'); window.location='clientfp.php';</script>";
          exit;
        } else {
          echo "<script>alert('❌ Failed to update password.');</script>";
        }
      } else {
        echo "<script>alert('⚠️ New passwords do not match.');</script>";
      }
    } else {
      echo "<script>alert('❌ Current password is incorrect.');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Client Dashboard | S&D CRE8TIVE</title>
  <link rel="stylesheet" href="client.css" />
</head>
<body>
  <header class="header">
    <div class="logo">S&D <span>CRE8TIVE</span></div>
    <a href="../login.html" class="logout-btn">Logout</a>
  </header>

  <main class="content">
    <section class="welcome">
      <h2>Welcome, <span><?php echo htmlspecialchars($username); ?></span> 🎉</h2>
      <p>Your role: <strong><?php echo htmlspecialchars($role); ?></strong></p>
    </section>

    <section class="dashboard-card">
      <h3>Client Dashboard</h3>
      <ul class="menu-list">
        <li onclick="showSection('createEvent')">📅 Create Event</li>
        <li onclick="showSection('myEvents')">🗂 My Events</li>
        <li onclick="openPasswordModal()">🔑 Change Password</li>
        <li onclick="showSection('profile')">👤 Profile</li>
      </ul>

      <!-- Create Event -->
      <div class="section" id="createEvent">
        <button class="btn-primary" id="bookEventBtn">✨ Book an Event</button>

        <div class="form-section" id="eventTypeSection">
          <h3>🎈 Select Event Type</h3>
          <select id="eventType" onchange="showForm()">
            <option value="">-- Select Event Type --</option>
            <option value="wedding">💍 Wedding</option>
            <option value="birthday">🎂 Birthday</option>
            <option value="christening">👶 Christening</option>
            <option value="other">🎉 Other Parties</option>
          </select>
        </div>

        <div id="eventForms">
          <!-- Wedding Form -->
          <div class="form-section" id="weddingForm">
            <h3>💍 Wedding Event Form</h3>
            <form>
              <label>Bride & Groom Names</label>
              <input type="text" placeholder="e.g. John & Jane" required />
              <label>Wedding Date</label>
              <input type="date" required />
              <label>Venue</label>
              <input type="text" placeholder="e.g. Grand Hotel, Manila" />
              <label>Guest Count</label>
              <input type="number" min="1" />
              <label>Special Requests</label>
              <textarea rows="3" placeholder="Any special setup?"></textarea>
              <button type="submit" class="btn-submit">Submit Booking</button>
            </form>
          </div>

          <!-- Birthday Form -->
          <div class="form-section" id="birthdayForm">
            <h3>🎂 Birthday Event Form</h3>
            <form>
              <label>Celebrant’s Name</label>
              <input type="text" required />
              <label>Age</label>
              <input type="number" min="1" required />
              <label>Event Date</label>
              <input type="date" required />
              <label>Theme</label>
              <input type="text" placeholder="e.g. Marvel, Barbie, etc." />
              <label>Special Requests</label>
              <textarea rows="3"></textarea>
              <button type="submit" class="btn-submit">Submit Booking</button>
            </form>
          </div>

          <!-- Christening Form -->
          <div class="form-section" id="christeningForm">
            <h3>👶 Christening Event Form</h3>
            <form>
              <label>Baby’s Name</label>
              <input type="text" required />
              <label>Date of Christening</label>
              <input type="date" required />
              <label>Church Name</label>
              <input type="text" />
              <label>Reception Venue</label>
              <input type="text" />
              <button type="submit" class="btn-submit">Submit Booking</button>
            </form>
          </div>

          <!-- Other Event Form -->
          <div class="form-section" id="otherForm">
            <h3>🎉 Other Event Form</h3>
            <form>
              <label>Event Name</label>
              <input type="text" placeholder="e.g. Anniversary, Corporate Party" />
              <label>Date</label>
              <input type="date" />
              <label>Venue</label>
              <input type="text" />
              <label>Event Details</label>
              <textarea rows="3"></textarea>
              <button type="submit" class="btn-submit">Submit Booking</button>
            </form>
          </div>
        </div>
      </div>

      <!-- My Events -->
      <div class="section" id="myEvents">
        <h3>🗂 My Events</h3>
        <p>No events booked yet. Once you book, they’ll appear here!</p>
      </div>

      <!-- Profile -->
      <div class="section" id="profile">
        <h3>👤 My Profile</h3>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($username); ?>@example.com</p>
        <p><strong>Member since:</strong> 2025</p>
      </div>
    </section>
  </main>

  <!-- Password Modal -->
  <div class="modal" id="passwordModal">
    <div class="modal-content">
      <span class="close" onclick="closePasswordModal()">&times;</span>
      <h3>🔑 Change Password</h3>
      <form method="POST">
        <label>Current Password</label>
        <input type="password" name="current" required />
        <label>New Password</label>
        <input type="password" name="new" required />
        <label>Confirm New Password</label>
        <input type="password" name="confirm" required />
        <button type="submit" class="btn-submit">Update Password</button>
      </form>
    </div>
  </div>

  <footer>
    © <?php echo date("Y"); ?> S&D CRE8TIVE. All rights reserved.
  </footer>

  <script>
    // Toggle sections
    function showSection(id) {
      document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));
      document.getElementById(id).classList.add("active");
    }

    // Event booking
    const bookBtn = document.getElementById("bookEventBtn");
    const eventTypeSection = document.getElementById("eventTypeSection");
    const forms = ["weddingForm", "birthdayForm", "christeningForm", "otherForm"];

    bookBtn.addEventListener("click", () => {
      eventTypeSection.classList.add("active");
      eventTypeSection.scrollIntoView({ behavior: "smooth" });
    });

    function showForm() {
      const selected = document.getElementById("eventType").value;
      forms.forEach(id => document.getElementById(id).classList.remove("active"));
      if (selected) {
        document.getElementById(selected + "Form").classList.add("active");
      }
    }

    // Password Modal
    function openPasswordModal() { document.getElementById("passwordModal").style.display = "flex"; }
    function closePasswordModal() { document.getElementById("passwordModal").style.display = "none"; }

    window.onclick = function(e) {
      if (e.target == document.getElementById("passwordModal")) closePasswordModal();
    };
  </script>
</body>
</html>
