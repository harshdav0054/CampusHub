function showSection(id) {
  const sections = document.querySelectorAll(".section");
  sections.forEach(sec => sec.style.display = "none");
  document.getElementById(id).style.display = "block";
}

function logout() {
  alert("Logging out...");
  window.location.href = "../home page/homepag.html";
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

// ✅ Assume stored old password (simulate DB value)
const storedOldPassword = "12345"; 

// Form validation
document.getElementById("editProfileForm").addEventListener("submit", function(e) {
  e.preventDefault(); 
  const oldPass = document.getElementById("oldPassword").value;
  const newPass = document.getElementById("newPassword").value;
  const confirmPass = document.getElementById("confirmPassword").value;
  const errorEl = document.getElementById("passwordError");

  if (oldPass !== storedOldPassword) {
    errorEl.textContent = "Old password is incorrect!";
    errorEl.style.display = "block";
    return;
  }

  if (newPass !== confirmPass) {
    errorEl.textContent = "New Password and Confirm Password do not match!";
    errorEl.style.display = "block";
    return;
  }

  errorEl.style.display = "none";
  alert("Password changed successfully!");
  // TODO: send data to PHP/MySQL backend
});
