<?php
session_start();

// Database connection
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "sd_cre8tive";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);
  $role = isset($_POST["role"]) ? trim($_POST["role"]) : "";

  // Choose table based on role
  switch ($role) {
    case "client":
      $table = "client";
      $redirect = "client/clientfp.php";
      break;
    case "staff":
      $table = "users";
      $redirect = "staff/stafffp.php";
      break;
    case "admin":
      $table = "account";
      $redirect = "admin/frontpage.php";
      break;
    default:
      echo "<script>alert('Invalid role selected!'); window.history.back();</script>";
      exit;
  }

  // Prepare and execute SQL
  $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ? AND password = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $role;

    echo "<script>
      alert('Login successful! Welcome, $role!');
      window.location.href = '$redirect';
    </script>";
  } else {
    echo "<script>
      alert('Invalid username or password!');
      window.history.back();
    </script>";
  }

  $stmt->close();
}

$conn->close();
?>
