// Toggle password visibility
function togglePassword(fieldId, icon) {
  const field = document.getElementById(fieldId);
  const isPassword = field.type === "password";
  field.type = isPassword ? "text" : "password";
  icon.classList.toggle("fa-eye");
  icon.classList.toggle("fa-eye-slash");
}

// Registration form validation and success popup
document.getElementById("registrationForm").addEventListener("submit", function (e) {
  e.preventDefault(); // prevent real submission for now

  const fullName = this.fullname.value.trim();
  const email = this.email.value.trim();
  const phone = this.phone.value.trim();
  const password = this.password.value;
  const confirmPassword = this.confirm_password.value;

  // Validate fields
  if (!fullName || !email || !phone || !password || !confirmPassword) {
    alert("Please fill in all fields.");
    return;
  }

  // Email format validation
  const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!emailPattern.test(email)) {
    alert("Please enter a valid email address.");
    return;
  }

  // Phone number validation
  const phonePattern = /^[0-9]{10}$/;
  if (!phonePattern.test(phone)) {
    alert("Please enter a valid 10-digit phone number.");
    return;
  }

  // Password length
  if (password.length < 6) {
    alert("Password must be at least 6 characters.");
    return;
  }

  // Password match
  if (password !== confirmPassword) {
    alert("Passwords do not match.");
    return;
  }

  // ✅ Show success popup
  const popup = document.getElementById("registrationSuccessPopup");
  popup.classList.add("show");

  // Redirect after 3 seconds
  setTimeout(() => {
    popup.classList.remove("show");
    window.location.href = "homepag.html"; // redirect to homepage
  }, 3000);
});
