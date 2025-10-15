document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contactForm");
  const msg = document.getElementById("formMessage");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("sendMail.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      if (data === "success") {
        msg.textContent = " Thank you! Your message has been sent.";
        msg.className = "msg success";
        form.reset();
      } else {
        msg.textContent = " Oops! Something went wrong. Please try again.";
        msg.className = "msg error";
      }
    })
    .catch(() => {
      msg.textContent = " Server error. Please try again later.";
      msg.className = "msg error";
    });
  });
});
