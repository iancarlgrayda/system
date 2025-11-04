<?php
session_start();

// 🔒 Restrict access: only logged-in admins allowed
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
  header("Location: ../login.html");
  exit();
}

$username = $_SESSION["username"];
$role = $_SESSION["role"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Front Page | S&D CRE8TIVE</title>
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: linear-gradient(to bottom right, #f8faff, #e9eefb);
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #004aad;
      color: white;
      padding: 20px 50px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    header h1 {
      font-size: 24px;
      letter-spacing: 1px;
    }

    .logout-btn {
      background: #ff4747;
      color: white;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 6px;
      font-weight: 600;
      transition: 0.3s;
      border: none;
      cursor: pointer;
    }

    .logout-btn:hover {
      background: #e63636;
      transform: scale(1.05);
    }

    .content {
      padding: 60px;
      text-align: center;
      color: #333;
    }

    .content h2 {
      color: #004aad;
      font-size: 32px;
      margin-bottom: 10px;
    }

    .card {
      background: white;
      max-width: 700px;
      margin: 40px auto;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
      text-align: left;
      animation: fadeIn 1s ease-in-out;
    }

    .card h3 {
      color: #004aad;
      margin-bottom: 15px;
    }

    .card p {
      font-size: 17px;
      line-height: 1.6;
      color: #555;
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #004aad;
      color: white;
      position: fixed;
      bottom: 0;
      width: 100%;
      font-size: 14px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <header>
    <h1>S&D CRE8TIVE SYSTEM</h1>
    <!-- Logout form that redirects to login.html -->
    <form method="post" style="margin:0;">
      <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
  </header>

  <div class="content">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Your role: <strong><?php echo htmlspecialchars($role); ?></strong></p>

    <div class="card">
      <h3>Dashboard Overview</h3>
      <p>
        This is your admin front page after login. You can add system modules here such as:
      </p>
      <ul>
        <li>📋 Manage Events or Clients</li>
        <li>📊 View System Reports</li>
        <li>⚙️ Access Admin Tools</li>
        <li>👤 Update Profile Information</li>
      </ul>
    </div>
  </div>

  <footer>
    © <?php echo date("Y"); ?> S&D CRE8TIVE. All rights reserved.
  </footer>
</body>
</html>

<?php
// 🧠 If logout button is pressed, end session and go to login.html
if (isset($_POST['logout'])) {
  session_unset();
  session_destroy();
  header("Location:  ../login.html");
  exit();
}
?>
