document.addEventListener("DOMContentLoaded", () => {
  const courses = [
    { title: "HTML & CSS Basics", progress: 75 },
    { title: "JavaScript Fundamentals", progress: 40 },
    { title: "PHP & MySQL Beginner", progress: 90 },
    { title: "Laravel Essentials", progress: 60 }
  ];

  const courseList = document.getElementById("enrolledCourses");

  courses.forEach(course => {
    const li = document.createElement("li");
    li.className = "list-group-item border-0 px-0"; // removes border and padding for clean center look

    li.innerHTML = `
      <div class="course-block mb-4">
        <div class="d-flex justify-content-between mb-1">
          <strong>${course.title}</strong>
          <span class="text-muted">${course.progress}%</span>
        </div>
        <div class="progress" style="height: 20px; background-color: #e9ecef;">
          <div class="progress-bar bg-primary progress-animated" 
              role="progressbar" 
              style="width: 0%;" 
              data-target="${course.progress}">
            0%
          </div>
        </div>
      </div>
    `;

    courseList.appendChild(li);
  });

  // Animate the progress bars
  setTimeout(() => {
    const bars = document.querySelectorAll(".progress-animated");
    bars.forEach(bar => {
      const target = +bar.getAttribute("data-target");
      let current = 0;

      const interval = setInterval(() => {
        if (current >= target) {
          clearInterval(interval);
        } else {
          current++;
          bar.style.width = current + "%";
          bar.innerText = current + "%";
        }
      }, 15);
    });
  }, 300);
});
