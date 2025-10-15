<?php
session_start();

include("../db.php"); 

$email_error = "";
$password_error = "";
$confirm_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $college_email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $confirm_error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM college_accounts WHERE email = ?");
        $stmt->bind_param("s", $college_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $email_error = "College already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_stmt = $conn->prepare("INSERT INTO college_accounts (email, password) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $college_email, $hashed_password);

            if ($insert_stmt->execute()) {
                $college_account_id = $insert_stmt->insert_id;

                if ($college_account_id > 0) {
                    $profile_stmt = $conn->prepare("INSERT INTO college_profiles (college_account_id) VALUES (?)");
                    $profile_stmt->bind_param("i", $college_account_id);

                    if ($profile_stmt->execute()) {
                        $_SESSION['college_id'] = $college_account_id;
                        $_SESSION['college_email'] = $college_email;
                        header("Location: ../collegedashboard/collegedashboard.php");
                        exit();
                    } else {
                        $password_error = "Profile creation failed. Try again.";
                    }
                }
            } else {
                $password_error = "Account creation failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>CampusHub | College Registration</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="clg-signup.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  </head>
  <body>
    <section class="registration-section">
      <div class="registration-container">
        <div class="registration-form">
          <h2>Register as an Institution</h2>

          <form id="collegeRegForm" action="" method="POST">
            <div class="input-icon">
              <i class="fas fa-university"></i>
              <input type="email" name="email" placeholder="College E-mail" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
            </div>
            <?php if (!empty($email_error)) { echo "<p class='error-msg'>$email_error</p>"; } ?>

            <div class="input-icon password-wrapper">
              <i class="fas fa-lock"></i>
              <input type="password" id="password" name="password" placeholder="Password" required />
              <span class="toggle-icon" onclick="togglePassword('password', this)">
                <i class="fas fa-eye"></i>
              </span>
            </div>
            <?php if (!empty($password_error)) { echo "<p class='error-msg'>$password_error</p>"; } ?>

            <div class="input-icon password-wrapper">
              <i class="fas fa-lock"></i>
              <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
              <span class="toggle-icon" onclick="togglePassword('confirm_password', this)">
                <i class="fas fa-eye"></i>
              </span>
            </div>
            <?php if (!empty($confirm_error)) { echo "<p class='error-msg'>$confirm_error</p>"; } ?>

            <button type="submit">Register College</button>
            <p>Already have an account? 
              <a href="../college-login/college-loginpage.php">Log In</a>
            </p>
          </form>
        </div>
        <div class="registration-image"></div>
      </div>
    </section>

    <script>
    function togglePassword(fieldId, iconSpan) {
      const input = document.getElementById(fieldId);
      const icon = iconSpan.querySelector('i');
      input.type = input.type === "password" ? "text" : "password";
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    }
    </script>
  </body>
</html>
