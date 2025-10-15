<?php
session_start();
include '../db.php';

if (!isset($_SESSION['college_id'])) {
    header("Location: ../college-login/college-loginpage.php");
    exit();
}

$college_account_id = $_SESSION['college_id'];
$active_tab = $_POST['active_tab'] ?? 'basic';

$sql = "SELECT * FROM college_profiles WHERE college_account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $college_account_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toast = "Information saved successfully!";

    if (isset($_POST['save_basic'])) {
        $collegeName = $_POST['collegeName'] ?? '';
        $address = $_POST['address'] ?? '';
        $website = $_POST['website'] ?? '';
        if ($profile) {
            $update = $conn->prepare("UPDATE college_profiles SET college_name=?, address=?, website=? WHERE id=?");
            $update->bind_param("sssi", $collegeName, $address, $website, $profile['id']);
            $update->execute();
        } else {
            $insert = $conn->prepare("INSERT INTO college_profiles (college_account_id, college_name, address, website) VALUES (?,?,?,?)");
            $insert->bind_param("isss", $college_account_id, $collegeName, $address, $website);
            $insert->execute();
        }
    }

    if (isset($_POST['save_about'])) {
        $about = $_POST['aboutText'] ?? '';
        if ($profile) {
            $update = $conn->prepare("UPDATE college_profiles SET about=? WHERE id=?");
            $update->bind_param("si", $about, $profile['id']);
            $update->execute();
        }
    }

    if (isset($_POST['save_logo']) && isset($_FILES['logoFile']) && $_FILES['logoFile']['name'] !== '') {
        if (!is_dir("uploads")) mkdir("uploads", 0755);
        $fileName = time() . "_" . basename($_FILES["logoFile"]["name"]);
        $targetFile = "uploads/" . $fileName;
        if (move_uploaded_file($_FILES["logoFile"]["tmp_name"], $targetFile)) {
            if ($profile) {
                $update = $conn->prepare("UPDATE college_profiles SET logo=? WHERE id=?");
                $update->bind_param("si", $fileName, $profile['id']);
                $update->execute();
            }
        }
    }

    if (isset($_POST['save_course'])) {
        $courseName = $_POST['courseName'] ?? '';
        if ($profile) {
            $update = $conn->prepare("UPDATE college_profiles SET course_name=? WHERE id=?");
            $update->bind_param("si", $courseName, $profile['id']);
            $update->execute();
        }
    }

    if (isset($_POST['send_approval']) && $profile) {
        $check = $conn->prepare("SELECT id FROM approvals WHERE college_id=? AND status='pending'");
        $check->bind_param("i", $profile['id']);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO approvals (college_id) VALUES (?)");
            $insert->bind_param("i", $profile['id']);
            $insert->execute();
        }
        $toast = "✅ Sent to admin for approval!";
    }

    if (isset($_POST['update_password'])) {
        $oldPassword = $_POST['oldPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        $sql = "SELECT password FROM college_accounts WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $college_account_id);
        $stmt->execute();
        $stmt->bind_result($currentPasswordHash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($oldPassword, $currentPasswordHash)) {
            $toast = "❌ Old password is incorrect!";
        } elseif ($newPassword !== $confirmPassword) {
            $toast = "⚠️ New passwords do not match!";
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE college_accounts SET password=? WHERE id=?");
            $update->bind_param("si", $newHash, $college_account_id);
            $update->execute();
            $toast = "✅ Password updated successfully!";
        }
    }

    $_SESSION['toast'] = $toast;
    $_SESSION['active_tab'] = $active_tab;
    header("Location: collegedashboard.php");
    exit();
}

$sql = "SELECT * FROM college_profiles WHERE college_account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $college_account_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$approval_status = "draft";
if ($profile) {
    $sql = "SELECT status FROM approvals WHERE college_id=? ORDER BY date DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $profile['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $approval_status = $row['status'];
    }
}

$toast = $_SESSION['toast'] ?? "";
$active_tab = $_SESSION['active_tab'] ?? "basic";
unset($_SESSION['toast'], $_SESSION['active_tab']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>CampusHub College Dashboard</title>
        <link rel="icon" type="image/png" href="favicon.png">
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body 
        data-toast="<?= htmlspecialchars($toast) ?>" 
        data-active-tab="<?= htmlspecialchars($active_tab) ?>" 
        data-approval="<?= htmlspecialchars($approval_status) ?>">

        <div class="sidebar">
            <h2>CampusHub</h2>
            <ul>
            <li onclick="showSection('profileDetails')"><i class="fas fa-school"></i> College Info</li>
            <li onclick="showSection('account')"><i class="fas fa-user"></i> My Account</li>
            <li class="logout"><button onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</button></li>
            </ul>
        </div>

        <div class="main-content">
            <!-- College Info Section -->
            <section id="profileDetails" class="section">
                <h2><i class="fas fa-building-columns"></i> College Info</h2>

                    <div class="form-tab-container">
                        <aside class="form-tabs">
                            <button onclick="showFormPart('basic')"><i class="fas fa-info-circle"></i> Basic Info</button>
                            <button onclick="showFormPart('logo')"><i class="fas fa-image"></i> Upload Logo</button>
                            <button onclick="showFormPart('courses')"><i class="fas fa-book"></i> Courses</button>
                            <button onclick="showFormPart('about')"><i class="fas fa-align-left"></i> About</button>
                        </aside>

                        <div class="form-content">
                            <!-- Basic Info -->
                            <form class="form-part" id="basic" method="POST">
                                <input type="hidden" name="active_tab" value="basic">
                                <h3><i class="fas fa-info-circle"></i> Basic Info</h3>
                                <div class="input-group">
                                    <i class="fas fa-university"></i>
                                    <input type="text" name="collegeName" 
                                        value="<?= htmlspecialchars($profile['college_name'] ?? '') ?>" 
                                        placeholder="College Name" required />
                                </div>
                                <div class="input-group">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <input type="text" name="address" 
                                        value="<?= htmlspecialchars($profile['address'] ?? '') ?>" 
                                        placeholder="Address" required />
                                </div>
                                <div class="input-group">
                                    <i class="fas fa-globe"></i>
                                    <input type="text" name="website" 
                                        value="<?= htmlspecialchars($profile['website'] ?? '') ?>" 
                                        placeholder="Website URL" required />
                                </div>
                                <button type="submit" name="save_basic"><i class="fas fa-save"></i> Save</button>
                            </form>

                            <!-- Logo Upload -->
                            <form class="form-part" id="logo" style="display:none;" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="active_tab" value="logo">
                                <h3><i class="fas fa-image"></i> Upload Logo / Image</h3>
                                <div class="input-group">
                                    <i class="fas fa-image"></i>
                                    <input type="file" name="logoFile" accept="image/*" required />
                                </div>
                                <?php if(!empty($profile['logo']) && file_exists("uploads/".$profile['logo'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($profile['logo']) ?>" width="120" style="margin:10px 0;">
                                <?php endif; ?>
                                <button type="submit" name="save_logo"><i class="fas fa-save"></i> Save</button>
                            </form>

                            <!-- Courses -->
                            <form class="form-part" id="courses" style="display:none;" method="POST">
                                <input type="hidden" name="active_tab" value="courses">
                                <h3><i class="fas fa-book"></i> Course Details</h3>
                                <div class="input-group">
                                    <i class="fas fa-book-open"></i>
                                    <input type="text" name="courseName" 
                                        value="<?= htmlspecialchars($profile['course_name'] ?? '') ?>" 
                                        placeholder="Course Name" required />
                                </div>
                                <button type="submit" name="save_course"><i class="fas fa-save"></i> Save</button>
                            </form>

                            <!-- About -->
                            <form class="form-part" id="about" style="display:none;" method="POST">
                                <input type="hidden" name="active_tab" value="about">
                                <h3><i class="fas fa-align-left"></i> About</h3>
                                <div class="input-group">
                                    <i class="fas fa-info-circle"></i>
                                    <textarea name="aboutText" placeholder="About the college" rows="4" required><?= htmlspecialchars($profile['about'] ?? '') ?></textarea>
                                </div>
                                <button type="submit" name="save_about"><i class="fas fa-save"></i> Save</button>

                                <div id="approvalSection" style="text-align:center; margin:20px 0;">
                                    <button type="submit" name="send_approval"><i class="fas fa-paper-plane"></i> Send to Approval</button>
                                </div>
                            </form>
                        </div>
                    </div>
            </section>

            <!-- Account Section -->
            <section id="account" class="section">
            <h2><i class="fas fa-user-cog"></i> My Account (College Admin Info)</h2>
            <form method="POST">
                <input type="hidden" name="active_tab" value="account">
                <h3><i class="fas fa-user-lock"></i> Login Credentials</h3>
                <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" value="<?= $_SESSION['college_email'] ?>" readonly />
                </div>
                <div class="input-group password-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="oldPassword" id="oldPassword" placeholder="Old Password" required />
                <span class="toggle-password" onclick="togglePassword('oldPassword', this)"><i class="fas fa-eye"></i></span>
                </div>
                <div class="input-group password-group">
                <i class="fas fa-key"></i>
                <input type="password" name="newPassword" id="newPassword" placeholder="New Password" required />
                <span class="toggle-password" onclick="togglePassword('newPassword', this)"><i class="fas fa-eye"></i></span>
                </div>
                <div class="input-group password-group">
                <i class="fas fa-key"></i>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required />
                <span class="toggle-password" onclick="togglePassword('confirmPassword', this)"><i class="fas fa-eye"></i></span>
                </div>
                <button type="submit" name="update_password"><i class="fas fa-user-edit"></i> Update Password</button>
            </form>
            </section>
        </div>

        <script src="script.js"></script>
    </body>
</html>
