<?php
session_start();
include '../db.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$course = isset($_GET['course']) ? trim($_GET['course']) : "";

$isStudentLoggedIn = false;
$studentName = "";

if (isset($_SESSION['student_id'])) {
    $studentId = $_SESSION['student_id'];
    $result = mysqli_query($conn, "SELECT fullname FROM students WHERE id='$studentId' LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $isStudentLoggedIn = true;
        $studentName = $row['fullname'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CampusHub - College Listing</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  </head>
  <body>
    <header class="navbar">
        <div class="nav-logo">
          <a href="../homepage/homepage.php">
            <div class="logo"></div>
          </a>
        </div>
        <nav class="nav-links">
          <a href="../homepage/homepage.php">Home</a>
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

    <section class="intro-heading">
      <h2>Discover Your Dream College</h2>
    </section>

    <div class="container">
      <div class="main-content">

        <!-- Search Form -->
        <form method="GET" class="search-container">
          <input type="text" name="q" class="search-input" 
                placeholder="Search by college, course, city..." 
                value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>

        <div class="college-grid">
          <?php
          $sql = "SELECT cp.* 
                  FROM college_profiles cp
                  INNER JOIN approvals ap ON cp.id = ap.college_id
                  WHERE ap.status = 'approved'";

          if ($search !== "") {
              $searchEscaped = mysqli_real_escape_string($conn, $search);
              $sql .= " AND (cp.college_name LIKE '%$searchEscaped%' 
                        OR cp.course_name LIKE '%$searchEscaped%' 
                        OR cp.address LIKE '%$searchEscaped%')";
          }

          if ($course !== "") {
              $courseEscaped = mysqli_real_escape_string($conn, $course);
              $sql .= " AND cp.course_name = '$courseEscaped'";
          }

          $result = mysqli_query($conn, $sql);
          $cardsLoaded = 0;

          if ($result && mysqli_num_rows($result) > 0) {
              while ($college = mysqli_fetch_assoc($result)) {
                  $cardsLoaded++;
                  $logoPath = !empty($college['logo'])
                      ? '../collegedashboard/uploads/' . basename($college['logo'])
                      : 'https://www.shutterstock.com/image-vector/college-logo-design-template-vector-600nw-2312781311.jpg';
                  $website = $college['website'];
                  if (!empty($website) && !preg_match("~^(?:f|ht)tps?://~i", $website)) {
                      $website = "http://" . $website;
                  }

                  echo '<div class="college-card">';
                  echo '<img src="'.$logoPath.'" alt="'.$college['college_name'].' Logo" />';
                  echo '<h4>'.$college['college_name'].'</h4>';
                  echo '<p class="location">Location: '.$college['address'].'</p>';
                  echo '<p class="desc">'.(!empty($college['about']) ? $college['about'] : 'No description available.').'</p>';
                  echo '<div class="info"><span>Courses: '.(!empty($college['course_name']) ? strtoupper($college['course_name']) : 'N/A').'</span></div>';
                  echo !empty($college['website'])
                      ? '<a href="'.$website.'" target="_blank" class="more-info-btn">Click for more info</a>'
                      : '<a href="#" class="more-info-btn disabled">No website available</a>';
                  echo '</div>';
              }
          }
          ?>
        </div>

        <p id="noResults" class="no-results" 
          style="display: <?php echo $cardsLoaded === 0 ? 'block' : 'none'; ?>;">
          No colleges found matching your search.
        </p>

      </div>
    </div>

    <footer class="footer">
        <div class="footer-logo">
          <a href="../homepage/homepage.php">
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
      <p class="copyright">© <?php echo date("Y"); ?> CampusHub. All Rights Reserved.</p>
    </footer>
    <script src="script.js"></script>
  </body>
</html>
