<?php
session_start();
include '../db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../student-login/student-loginpage.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$fullname = $email = "";
$toast = "";

$sql = "SELECT * FROM students WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($result)) {
    $fullname = $row['fullname'];
    $email = $row['email'];
    $storedPassword = $row['password']; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPass = $_POST['oldPassword'] ?? '';
    $newPass = $_POST['newPassword'] ?? '';
    $confirmPass = $_POST['confirmPassword'] ?? '';
    $fullNameInput = $_POST['fullName'] ?? '';

    if (!password_verify($oldPass, $storedPassword)) {
        $toast = "❌ Old password is incorrect!";
    } elseif ($newPass !== $confirmPass) {
        $toast = "⚠️ New Password and Confirm Password do not match!";
    } else {
        $hashedNewPass = password_hash($newPass, PASSWORD_DEFAULT);
        $updateSql = "UPDATE students SET fullname=?, password=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmt, "ssi", $fullNameInput, $hashedNewPass, $student_id);
        mysqli_stmt_execute($stmt);

        $fullname = $fullNameInput;
        $toast = "✅ Password changed successfully!";
    }

    $_SESSION['toast'] = $toast;
    header("Location: studentdashboard.php");
    exit();
}

$toast = $_SESSION['toast'] ?? "";
unset($_SESSION['toast']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Dashboard</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body data-toast="<?= htmlspecialchars($toast) ?>">

<nav class="navbar">
  <div class="logo">CampusHub</div>
  <div class="nav-right">
    <button class="logout-btn" onclick="confirmLogout()">Logout</button>
  </div>
</nav>

<header class="welcome">
  <h1>Welcome</h1>
  <p>Select a section below to manage your dashboard</p>
</header>

<section class="card-container">
  <a href="../searchpage/searchpage.php" class="card">
    <i class="fas fa-search"></i><h3>Explore Colleges</h3>
  </a>
  <div class="card" onclick="showSection('profile')">
    <i class="fas fa-user-edit"></i><h3>Edit Profile</h3>
  </div>
</section>

<section class="content-section">
  <div id="profile" class="section">
    <h2>Edit Profile</h2>

    <form id="editProfileForm" method="post">
      <input type="text" placeholder="Full Name" name="fullName" id="fullName" value="<?= htmlspecialchars($fullname) ?>"/><br />
      <input type="email" placeholder="Email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" readonly /><br />

      <div class="password-container">
        <input type="password" placeholder="Old Password" name="oldPassword" id="oldPassword" />
        <i class="fas fa-eye toggle-eye" onclick="togglePassword('oldPassword', this)"></i>
      </div>

      <div class="password-container">
        <input type="password" placeholder="New Password" name="newPassword" id="newPassword" />
        <i class="fas fa-eye toggle-eye" onclick="togglePassword('newPassword', this)"></i>
      </div>

      <div class="password-container">
        <input type="password" placeholder="Confirm Password" name="confirmPassword" id="confirmPassword" />
        <i class="fas fa-eye toggle-eye" onclick="togglePassword('confirmPassword', this)"></i>
      </div>

      <button type="submit">Save Changes</button>
    </form>
  </div>
</section>

<script>
function showSection(id) {
  document.querySelectorAll(".section").forEach(sec => sec.style.display = "none");
  document.getElementById(id).style.display = "block";
}

function togglePassword(id, el) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
    el.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    input.type = "password";
    el.classList.replace("fa-eye-slash", "fa-eye");
  }
}

function confirmLogout() {
  Swal.fire({
    title: "Are you sure?",
    text: "You will be logged out!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, logout"
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "logout.php";
    }
  });
}

// Show toast from PHP
document.addEventListener("DOMContentLoaded", () => {
  const toastMsg = document.body.getAttribute("data-toast");
  if (toastMsg) {
    let icon = "info";
    if (toastMsg.includes("✅")) icon = "success";
    else if (toastMsg.includes("❌")) icon = "error";
    else if (toastMsg.includes("⚠️")) icon = "warning";

    Swal.fire({
      title: "Notice",
      text: toastMsg,
      icon: icon,
      confirmButtonText: "OK"
    });
  }
});
</script>
</body>
</html>
