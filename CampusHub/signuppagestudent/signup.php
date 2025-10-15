<?php
session_start();
include '../db.php';

$error = "";
$success = "";
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM students WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO students (fullname, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $hashed_password);
            if (mysqli_stmt_execute($stmt)) {
                $student_id = mysqli_insert_id($conn);
                $_SESSION['student_id'] = $student_id;
                $_SESSION['student_name'] = $fullname;
                $_SESSION['student_email'] = $email;

                $success = "Registration successful!";
                $redirect = true; 
            } else {
                $error = "Error registering. Please try again.";
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
  <title>CampusHub | Sign Up</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="signup.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <section class="reg-section">
    <div class="reg-container">
      <div class="reg-image"></div>
      <div class="reg-form">
        <h2>Register on CampusHub</h2>

        <?php if(!empty($error)): ?>
            <div class="form-message error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(!empty($success)): ?>
            <div class="form-message success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form id="registrationForm" action="" method="POST">
          <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" name="fullname" placeholder="Full Name" required />
          </div>
          <div class="input-icon">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address" required />
          </div>
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Password" required />
            <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
          </div>
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
            <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)"></i>
          </div>
          <button type="submit">Sign Up</button>
          <p>Already have an account? <a href="../loginpage/login.php">Log In</a></p>
        </form>
      </div>
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
