<?php
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "sd_cre8tive";

// ✅ Connect to database
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $role = $_POST["role"];

  // 🔹 CLIENT REGISTRATION
  if ($role === "client") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
      echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
      exit;
    }

    // ⚠️ Plain password (visible)
    $stmt = $conn->prepare("INSERT INTO client (fullname, email, phone, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullname, $email, $phone, $username, $password);

    if ($stmt->execute()) {
      echo "<script>alert('Client registered successfully!'); window.location.href='login.html';</script>";
    } else {
      echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }
    $stmt->close();
  }

  // 🔹 STAFF REGISTRATION
  elseif ($role === "staff") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // ⚠️ Plain password (visible)
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'staff')");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
      echo "<script>alert('Staff registered successfully!'); window.location.href='login.html';</script>";
    } else {
      echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }
    $stmt->close();
  }
}

$conn->close();
?>
