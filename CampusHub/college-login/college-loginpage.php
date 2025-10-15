<?php
session_start();
require_once "../db.php"; 

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? "");
    $password = trim($_POST['password'] ?? "");

    if ($email === "" || $password === "") {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, email, password FROM college_accounts WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $db_email, $db_hash);
                $stmt->fetch();

                if (password_verify($password, $db_hash)) {
                    $_SESSION['college_id'] = $id;
                    $_SESSION['college_email'] = $db_email;
                    $success = "Login successful! Redirecting...";
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No account found with this email.";
            }
            $stmt->close();
        } else {
            $error = "Server error. Please try again.";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>CampusHub | Log In</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="clg-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  </head>
  <body>
    <section class="login-section">
      <div class="login-container">
        <div class="login-form">
          <h2>Log In as an Institution</h2>

          <?php if (!empty($success)) : ?>
            <div class="form-message success-msg"><?php echo $success; ?></div>
          <?php endif; ?>
          <?php if (!empty($error)) : ?>
            <div class="form-message error-msg"><?php echo $error; ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="input-icon">
              <i class="fas fa-envelope"></i>
              <input
                type="email"
                name="email"
                placeholder="College Email Address"
                required
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                autocomplete="username"
              />
            </div>

            <div class="input-icon">
              <i class="fas fa-lock"></i>
              <input
                type="password"
                id="login_password"
                name="password"
                placeholder="Password"
                required
                autocomplete="current-password"
                class="<?php echo !empty($error) ? 'input-error' : ''; ?>"
              />
              <i class="fas fa-eye password-toggle" onclick="togglePassword('login_password', this)"></i>
            </div>

            <button type="submit">Log In</button>
            <p>Don't have an account? <a href="../college-signup/clg-signup.php">Register as an Institution</a></p>
          </form>
        </div>
        <div class="login-image"></div>
      </div>
    </section>

    <script>
      function togglePassword(fieldId, iconEl) {
        const field = document.getElementById(fieldId);
        const isPassword = field.type === "password";
        field.type = isPassword ? "text" : "password";
        iconEl.classList.toggle("fa-eye");
        iconEl.classList.toggle("fa-eye-slash");
      }
    </script>

    <?php if (!empty($success)) : ?>
    <script>
      setTimeout(function(){
        window.location.href = "../collegedashboard/collegedashboard.php";
      }, 2000);
    </script>
    <?php endif; ?>
  </body>
</html>
