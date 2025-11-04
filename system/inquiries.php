<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "inquiries";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("❌ Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $service = $_POST['service'];
  $message = $_POST['message'];

  $stmt = $conn->prepare("INSERT INTO inquiries (name, email, service, message) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $service, $message);

  if ($stmt->execute()) {
    echo "✅ Inquiry sent successfully!";
  } else {
    echo "❌ Failed to send inquiry. Try again.";
  }

  $stmt->close();
}
$conn->close();
?>
