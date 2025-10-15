<?php
session_start();
include '../db.php';

$error = "";
$success = "";
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, fullname, password FROM students WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $id, $fullname, $hashed_password);

        if (mysqli_stmt_num_rows($stmt) == 0) {
            $error = "Email not registered.";
        } else {
            mysqli_stmt_fetch($stmt);
            if (password_verify($password, $hashed_password)) {
                $_SESSION['student_id'] = $id;
                $_SESSION['student_name'] = $fullname;
                $success = "Login successful! Redirecting...";
                $redirect = true; 
            } else {
                $error = "Incorrect password.";
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>CampusHub | Log In</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  </head>
  <body>
    <section class="login-section">
      <div class="login-container">
        <div class="login-form">
          <h2>Log In to CampusHub</h2>

          <?php if(!empty($error)): ?>
              <div class="form-message error-msg"><?php echo $error; ?></div>
          <?php endif; ?>
          <?php if(!empty($success)): ?>
              <div class="form-message success-msg"><?php echo $success; ?></div>
          <?php endif; ?>

          <form id="loginForm" method="POST" action="">
            <div class="input-icon">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" placeholder="Email Address" required />
            </div>
            <div class="input-icon">
              <i class="fas fa-lock"></i>
              <input type="password" id="login_password" name="password" placeholder="Password" required />
              <i class="fas fa-eye password-toggle" onclick="togglePassword('login_password', this)"></i>
            </div>
            <button type="submit">Log In</button>
            <p>Don't have an account? <a href="../signuppagestudent/signup.php">Sign Up</a></p>
          </form>
        </div>
        <div class="login-image"></div>
      </div>
    </section>

    <script>
      function togglePassword(fieldId, icon) {
        const field = document.getElementById(fieldId);
        const isPassword = field.type === "password";
        field.type = isPassword ? "text" : "password";
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
      }

      <?php if($redirect): ?>
      setTimeout(() => {
        window.location.href = "../studentdashboard/dashboard.php";
      }, 2000);
      <?php endif; ?>
    </script>
  </body>
</html>
