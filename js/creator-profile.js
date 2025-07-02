document.addEventListener("DOMContentLoaded", () => {
  const uploadedCourses = [
    { title: "Modern HTML & CSS", enrolled: 130 },
    { title: "Bootstrap UI Mastery", enrolled: 90 },
    { title: "Laravel Beginner to Pro", enrolled: 210 }
  ];

  const list = document.getElementById("uploadedCourses");

  uploadedCourses.forEach((course, index) => {
    const li = document.createElement("li");
    li.className = "list-group-item d-flex justify-content-between align-items-center fade-in";
    li.style.transitionDelay = `${index * 0.2}s`;

    li.innerHTML = `
      <strong>${course.title}</strong>
      <span class="badge bg-success rounded-pill badge-counting" data-count="${course.enrolled}">0 Enrolled</span>
    `;

    list.appendChild(li);

    // Trigger fade-in after short delay
    setTimeout(() => {
      li.classList.add("visible");
    }, 100);
  });

  // Animate count up
  setTimeout(() => {
    const counters = document.querySelectorAll(".badge-counting");
    counters.forEach(counter => {
      const target = +counter.getAttribute("data-count");
      let count = 0;

      const update = setInterval(() => {
        if (count >= target) {
          clearInterval(update);
          counter.textContent = `${target} Enrolled`;
        } else {
          count++;
          counter.textContent = `${count} Enrolled`;
        }
      }, 15);
    });
  }, 600);
});
