// ========== Page Navigation ==========
function showSection(id) {
  document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

// ========== Form Tabs ==========
function showFormPart(id) {
  document.querySelectorAll('.form-part').forEach(part => part.style.display = 'none');
  document.querySelectorAll('.form-tabs button').forEach(btn => btn.classList.remove('active'));
  document.getElementById(id).style.display = 'block';
  document.querySelector(`.form-tabs button[onclick*="${id}"]`)?.classList.add('active');

  // Remember last tab
  document.querySelectorAll("input[name='active_tab']").forEach(el => el.value = id);
}

// ========== Password Toggle ==========
function togglePassword(inputId, toggleIcon) {
  const input = document.getElementById(inputId);
  const icon = toggleIcon.querySelector("i");
  if (input.type === "password") {
    input.type = "text";
    icon.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.replace("fa-eye-slash", "fa-eye");
  }
}

// ========== DOM Ready ==========
document.addEventListener("DOMContentLoaded", () => {
  // Show College Info by default
  showSection("profileDetails");

  // Load active tab
  let activeTab = document.body.getAttribute("data-active-tab") || "basic";
  showFormPart(activeTab);

  // Approval check
  let approvalStatus = document.body.getAttribute("data-approval");
  if (approvalStatus === "pending" || approvalStatus === "draft") {
    document.getElementById("approvalSection").style.display = "block";
  } else {
    document.getElementById("approvalSection").style.display = "none";
  }

  // Toast from PHP (SweetAlert)
  let toastMsg = document.body.getAttribute("data-toast");
  if (toastMsg) {
    Swal.fire({
      title: "Notice",
      text: toastMsg,
      icon: "info",
      confirmButtonText: "OK"
    });
  }

  // Approval button popup
  const approvalBtn = document.querySelector("#approvalSection button");
  if (approvalBtn) {
    approvalBtn.addEventListener("click", () => {
      Swal.fire({
        title: "Approval Sent!",
        text: "Your profile has been submitted for admin approval.",
        icon: "success",
        confirmButtonText: "OK"
      });
    });
  }
});

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
      window.location.href = "../homepage/homepage.php";
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
        const toastMsg = document.body.getAttribute("data-toast");
        if (toastMsg) {
            Swal.fire({
            title: "Notice",
            text: toastMsg,
            icon: "info",
            confirmButtonText: "OK"
            });
        }
        });