<?php
session_start();
include("../db.php"); 

$error = "";
$success = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Plain text password check (since DB stores plain text)
        if ($password === $row['password']) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_email'] = $row['email'];
            $success = "Login successful! Redirecting...";
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login | CampusHub</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="admin-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  </head>
  <body>

    <div class="admin-login-wrapper">
      <div class="admin-login-container">

        <!-- Logo Side -->
        <div class="admin-logo-side">
          <img src="logomain1.png" alt="CampusHub Logo" />
        </div>

        <!-- Login Form Side -->
        <div class="admin-login-box">
          <h2><i class="fas fa-user-shield"></i> Admin Login</h2>

          <!-- Success & Error Messages -->
          <?php if (!empty($success)) : ?>
            <div class="form-message success-msg"><?php echo $success; ?></div>
          <?php endif; ?>
          <?php if (!empty($error)) : ?>
            <div class="form-message error-msg"><?php echo $error; ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group">
              <label for="adminEmail"><i class="fas fa-envelope"></i> Email</label>
              <input type="email" id="adminEmail" name="email" placeholder="Enter admin email" required />
            </div>
            <div class="form-group">
              <label for="adminPassword"><i class="fas fa-lock"></i> Password</label>
              <div class="password-wrapper">
                <input type="password" id="adminPassword" name="password" placeholder="Enter password" required />
                <i class="fas fa-eye password-toggle" onclick="togglePassword('adminPassword', this)"></i>
              </div>
            </div>

            <button type="submit" id="loginBtn">Login</button>
          </form>
        </div>
      </div>
    </div>

    <script>
    // Show/Hide Password
    function togglePassword(fieldId, iconElement) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
            iconElement.classList.remove("fa-eye");
            iconElement.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            iconElement.classList.remove("fa-eye-slash");
            iconElement.classList.add("fa-eye");
        }
    }

    // Redirect after success
    <?php if (!empty($success)) : ?>
      setTimeout(() => {
        window.location.href = "../admindashboard/admin.php";
      }, 2000); // redirect after 2 seconds
    <?php endif; ?>
    </script>
  </body>
</html>
