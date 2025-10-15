<?php
include '../db.php';

$formMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $formMessage = "Please fill in all required fields.";
        $msgClass = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $formMessage = "Thank you! Your message has been sent.";
            $msgClass = "success";
            $name = $email = $subject = $message = "";
        } else {
            $formMessage = "Oops! Something went wrong. Please try again.";
            $msgClass = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - CampusHub</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>Contact Us</h1>
    <p>We’d love to hear from you! Reach out with any questions.</p>
  </header>

  <div class="container">
    <div class="contact-info">
      <h2>Our Information</h2>
      <p><strong>Email:</strong> support@campushub.com</p>
      <p><strong>Phone:</strong> +91 98765 43210</p>
      <p><strong>Address:</strong> Plot 183,Sector 29,Gandhinagar  Gujarat  India</p>
    </div>

    <div class="contact-form">
      <h2>Send us a Message</h2>
      <?php if($formMessage !== ""): ?>
        <p class="msg <?= $msgClass ?>"><?= $formMessage ?></p>
      <?php endif; ?>
      <form method="POST">
        <input type="text" name="name" placeholder="Your Name" value="<?= htmlspecialchars($name ?? '') ?>" required>
        <input type="email" name="email" placeholder="Your Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        <input type="text" name="subject" placeholder="Subject" value="<?= htmlspecialchars($subject ?? '') ?>">
        <textarea name="message" placeholder="Your Message" required><?= htmlspecialchars($message ?? '') ?></textarea>
        <button type="submit">Send Message</button>
      </form>
    </div>
  </div>

</body>
</html>
