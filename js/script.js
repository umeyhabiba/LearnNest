document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  const title = document.getElementById("title");
  const description = document.getElementById("description");
  const video = document.getElementById("video");
  const notes = document.getElementById("notes");
  const assignment = document.getElementById("assignment");

  const maxVideoSize = 100 * 1024 * 1024;
  const maxPdfSize = 10 * 1024 * 1024;

  form.addEventListener("submit", function (e) {
    let errors = [];


    if (title.value.trim() === "") {
      errors.push("Course Title is required.");
    }


    if (description.value.trim() === "") {
      errors.push("Description is required.");
    }


    if (video.files.length === 0) {
      errors.push("Video file is required.");
    } else if (video.files[0].size > maxVideoSize) {
      errors.push("Video file must be under 100MB.");
    }


    if (notes.files.length > 0 && notes.files[0].size > maxPdfSize) {
      errors.push("Notes PDF must be under 10MB.");
    }


    const oldMsg = document.getElementById("form-messages");
    if (oldMsg) oldMsg.remove();

    if (errors.length > 0) {
      e.preventDefault();

      const msg = document.createElement("div");
      msg.id = "form-messages";
      msg.style.backgroundColor = "#ffe0e0";
      msg.style.color = "#900";
      msg.style.border = "1px solid #e60000";
      msg.style.padding = "10px";
      msg.style.marginBottom = "15px";
      msg.style.borderRadius = "8px";

      msg.innerHTML = `<strong>Please fix the following errors:</strong><ul style="margin-top: 10px;">${errors
        .map((err) => <li>${err}</li>)
        .join("")}</ul>`;

      form.prepend(msg);
    } else {
      alert("Course submitted successfully!");
    }
  });
});