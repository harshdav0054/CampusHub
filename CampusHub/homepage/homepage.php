<?php
session_start();

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$isStudentLoggedIn = isset($_SESSION['student_id']) && !empty($_SESSION['student_id']);
$studentName = $isStudentLoggedIn ? $_SESSION['student_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusHub</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  </head>
  <body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="nav-logo">
      <a href="homepage.php"><div class="logo"></div></a>
    </div>

    <nav class="nav-links">
      <a href="homepage.php">Home</a>
      <a href="../searchpage/searchpage.php">All Course</a>
      <a href="../AboutUS/index.html">About Us</a>
      <a href="../contectpage/contect.php">Contact</a>
      <a href="../college-login/college-loginpage.php">Join as an Institution</a>
    </nav>

    <div class="nav-buttons">
      <?php if($isStudentLoggedIn): ?>
          <div class="user-menu">
              <a href="../studentdashboard/dashboard.php" class="user-icon">
                  <i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($studentName) ?>
              </a>
          </div>
      <?php else: ?>
          <a href="../loginpage/login.php" class="login-btn">Log In</a>
          <a href="../signuppagestudent/signup.php" class="signup-btn">Sign Up</a>
      <?php endif; ?>
    </div>

  </header>

    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-buttons">
        <a href="../searchpage/searchpage.php" class="getstarted-btn">Explore College</a>
      </div>
    </div>

    <!-- Explore Verified Colleges -->
    <section class="campus-section">
      <h1>Explore <span>verified</span> colleges on CampusHub</h1>
      <p>Discover top institutions across various streams including BCA, BBA, B.COM, and more. Search, and make informed decisions for your academic future.</p>

      <div class="campus-grid">
        <a href="../searchpage/searchpage.php?course=BCA" class="campus-card">BCA<br><span>Colleges</span></a>
        <a href="../searchpage/searchpage.php?course=BBA" class="campus-card">BBA<br><span>Colleges</span></a>
        <a href="../searchpage/searchpage.php?course=BCOM" class="campus-card">B.COM<br><span>Colleges</span></a>
        <a href="../searchpage/searchpage.php?course=MCA" class="campus-card">MCA<br><span>Colleges</span></a>
        <a href="../searchpage/searchpage.php?course=MBA" class="campus-card">MBA<br><span>Colleges</span></a>
        <a href="../searchpage/searchpage.php?course=M.COM" class="campus-card">M.COM<br><span>Colleges</span></a>
        <a href="../searchpage/searchpage.php" class="campus-card all-campus">View All Colleges <span class="arrow">&rarr;</span></a>
      </div>
    </section>

    <!-- Career Section -->
    <section class="career-section">
      <div class="career-container">
        <div class="career-image">
          <img src="career-section.png" alt="Career Image">
        </div>
        <div class="career-text">
          <h2>Expand your <span class="italic">career</span><br><span class="italic">opportunities</span></h2>
          <p class="description">
            CampusHub provides a smart, student-friendly platform to discover and compare the best colleges across Gujarat.
             We aim to support your academic journey with verified data, reliable information, and personalized discovery tools.
          </p>
          <div class="features">
            <div class="feature-box">
              <div class="icon">📍</div>
              <div>
                <h4>Available in Gujarat State Only</h4>
                <p>CampusHub currently lists colleges and courses located exclusively within Gujarat to ensure region-focused results and updates.</p>
              </div>
            </div>
            <div class="feature-box">
              <div class="icon">🎓</div>
              <div>
                <h4>College Announcements</h4>
                <p>Get updates from registered colleges directly on CampusHub — including admissions, events, and application deadlines.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-logo">
        <a href="homepage.php">
          <img src="logomain1.png" alt="Logo" class="footer-logo-img">
        </a>
      </div>
      <ul class="footer-links">
        <li><a href="../AboutUS/index.html">About Us</a></li>
        <li><a href="../searchpage/searchpage.php">Courses</a></li>
        <li><a href="../college-login/college-loginpage.php">Join as an Institution</a></li>
        <li><a href="../admin-loginpage/admin-loginpage.php">Login as an Admin</a></li>
      </ul>
      <hr>
      <p class="copyright">
        © <?php echo date("Y"); ?> CampusHub. All Rights Reserved.
      </p>
    </footer>
  </body>
</html>
