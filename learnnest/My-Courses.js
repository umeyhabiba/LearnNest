// Global variables
let currentSearch = '';
let currentStatusFilter = 'all';
let allCourses = [];
let filteredCourses = [];
let currentPage = 1;
const coursesPerPage = 10;

// DOM Elements
const tableBody = document.getElementById("coursesTableBody");
const paginationEl = document.getElementById("coursesPagination");

// Initialize search and filter controls
function initControls() {
    const searchBox = document.createElement('input');
    searchBox.type = 'text';
    searchBox.className = 'form-control form-control-sm bg-dark text-light border-secondary ms-3';
    searchBox.placeholder = 'Search courses...';
    searchBox.style.maxWidth = '200px';
    searchBox.addEventListener('input', async (e) => {
        currentSearch = e.target.value.trim();
        currentPage = 1;
        
        // If search query is long enough, use server-side search
        if (currentSearch.length >= 3) {
            await searchCourses(currentSearch);
        } else {
            // Client-side filtering for short queries
            filterCourses();
        }
    });

    const statusFilter = document.createElement('select');
    statusFilter.className = 'form-select form-select-sm bg-dark text-light border-secondary ms-2';
    statusFilter.style.maxWidth = '150px';
    statusFilter.innerHTML = `
        <option value="all">All Statuses</option>
        <option value="Published">Published</option>
        <option value="Draft">Draft</option>
    `;
    statusFilter.addEventListener('change', (e) => {
        currentStatusFilter = e.target.value;
        currentPage = 1;
        filterCourses();
    });

    // Add controls container
    const controlsDiv = document.createElement('div');
    controlsDiv.className = 'd-flex justify-content-between align-items-center mb-3';
    controlsDiv.innerHTML = '<h2 class="mb-0 text-info">My Courses</h2>';
    const filterGroup = document.createElement('div');
    filterGroup.className = 'd-flex';
    filterGroup.appendChild(searchBox);
    filterGroup.appendChild(statusFilter);
    controlsDiv.appendChild(filterGroup);
    document.querySelector('.container-fluid').insertBefore(controlsDiv, document.querySelector('.table-responsive'));
}

// Server-side search
async function searchCourses(query) {
    try {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center"><div class="spinner-border text-secondary"></div></td></tr>`;
        
        const response = await fetch(`../learnnest-backend/api/search-courses.php?q=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        
        const result = await response.json();
        
        if (result.success) {
            filteredCourses = result.courses;
            renderCourses();
        } else {
            throw new Error(result.message || "Failed to search courses");
        }
    } catch (error) {
        console.error("Search error:", error);
        showToast(`❌ Search error: ${error.message}`, "danger");
        // Fall back to client-side filtering
        filterCourses();
    }
}

// Client-side filtering
function filterCourses() {
    filteredCourses = allCourses.filter(course => {
        const matchesSearch = currentSearch === '' || 
            (course.name && course.name.toLowerCase().includes(currentSearch.toLowerCase())) || 
            (course.description && course.description.toLowerCase().includes(currentSearch.toLowerCase()));
        const matchesStatus = currentStatusFilter === 'all' || course.status === currentStatusFilter;
        return matchesSearch && matchesStatus;
    });
    renderCourses();
}

// Load all courses from API
async function loadCourses() {
    try {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center"><div class="spinner-border text-secondary"></div></td></tr>`;
        
        const response = await fetch("../learnnest-backend/api/get-courses.php");
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        
        const result = await response.json();
        
        if (result.success && result.courses.length > 0) {
            allCourses = result.courses;
            filteredCourses = [...allCourses]; // Initialize filtered courses
            renderCourses();
        } else {
            tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No courses found</td></tr>`;
        }
    } catch (error) {
        console.error("Error loading courses:", error);
        tableBody.innerHTML = `<tr><td colspan="8" class="text-danger text-center">Error: ${error.message}</td></tr>`;
    }
}

// Render courses in table
function renderCourses() {
    tableBody.innerHTML = "";
    
    const startIndex = (currentPage - 1) * coursesPerPage;
    const endIndex = Math.min(startIndex + coursesPerPage, filteredCourses.length);
    
    if (filteredCourses.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No courses match your criteria</td></tr>`;
        renderPagination(filteredCourses.length);
        return;
    }

    for (let i = startIndex; i < endIndex; i++) {
        const course = filteredCourses[i];
        const row = document.createElement("tr");
        row.dataset.id = course.id;
        row.innerHTML = `
            <td>${i + 1}</td>
            <td>${course.name || course.title || 'Untitled'}</td>
            <td>${course.description || 'N/A'}</td>
            <td>${course.duration}</td>
            <td>$${course.price || '0.00'}</td>
            <td>${course.students}</td>
            <td><span class="badge ${course.status === 'Published' ? 'bg-success' : 'bg-warning text-dark'}">${course.status}</span></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="${course.id}" title="Edit Course">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${course.id}" title="Delete Course">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    }
    
    renderPagination(filteredCourses.length);
}


// Render pagination
function renderPagination(filteredCount) {
    paginationEl.innerHTML = "";
    const totalPages = Math.ceil(filteredCount / coursesPerPage);
    
    if (totalPages <= 1) return;
    
    // Previous button
    const prevBtn = document.createElement("button");
    prevBtn.className = `btn btn-sm mx-1 ${currentPage === 1 ? 'btn-secondary disabled' : 'btn-outline-info'}`;
    prevBtn.innerHTML = `<i class="fas fa-chevron-left"></i>`;
    prevBtn.addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            renderCourses();
        }
    });
    paginationEl.appendChild(prevBtn);
    
    // Page buttons
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    if (startPage > 1) {
        const firstBtn = document.createElement("button");
        firstBtn.className = 'btn btn-sm mx-1 btn-outline-info';
        firstBtn.textContent = '1';
        firstBtn.addEventListener("click", () => {
            currentPage = 1;
            renderCourses();
        });
        paginationEl.appendChild(firstBtn);
        
        if (startPage > 2) {
            const ellipsis = document.createElement("span");
            ellipsis.className = 'mx-1 align-self-center';
            ellipsis.textContent = '...';
            paginationEl.appendChild(ellipsis);
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement("button");
        btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-info' : 'btn-outline-info'}`;
        btn.textContent = i;
        btn.addEventListener("click", () => {
            currentPage = i;
            renderCourses();
        });
        paginationEl.appendChild(btn);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const ellipsis = document.createElement("span");
            ellipsis.className = 'mx-1 align-self-center';
            ellipsis.textContent = '...';
            paginationEl.appendChild(ellipsis);
        }
        
        const lastBtn = document.createElement("button");
        lastBtn.className = 'btn btn-sm mx-1 btn-outline-info';
        lastBtn.textContent = totalPages;
        lastBtn.addEventListener("click", () => {
            currentPage = totalPages;
            renderCourses();
        });
        paginationEl.appendChild(lastBtn);
    }
    
    // Next button
    const nextBtn = document.createElement("button");
    nextBtn.className = `btn btn-sm mx-1 ${currentPage === totalPages ? 'btn-secondary disabled' : 'btn-outline-info'}`;
    nextBtn.innerHTML = `<i class="fas fa-chevron-right"></i>`;
    nextBtn.addEventListener("click", () => {
        if (currentPage < totalPages) {
            currentPage++;
            renderCourses();
        }
    });
    paginationEl.appendChild(nextBtn);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    initControls();
    loadCourses();
    
    // Event delegation for edit/delete buttons
    document.addEventListener("click", async (e) => {
        const editBtn = e.target.closest(".edit-btn");
        const deleteBtn = e.target.closest(".delete-btn");
        
        if (editBtn) {
            const courseId = editBtn.getAttribute("data-id");
            const course = allCourses.find(c => c.id == courseId);
            if (course) {
                document.getElementById("editCourseId").value = course.id;
                document.getElementById("editCourseName").value = course.name;
                document.getElementById("editDescription").value = course.description;
                document.getElementById("editDuration").value = course.duration;
                document.getElementById("editPrice").value = course.price;
                document.getElementById("editStudents").value = course.students;
                document.getElementById("editStatus").value = course.status;
                
                new bootstrap.Modal(document.getElementById("editCourseModal")).show();
            }
        }
        
        if (deleteBtn) {
            const courseId = deleteBtn.getAttribute("data-id");
            const course = allCourses.find(c => c.id == courseId);
            if (course) {
                const confirmed = await showConfirmDialog(
                    `Delete Course "${course.name}"?`,
                    "This will permanently delete the course and all associated data. Are you sure?",
                    "Delete",
                    "Cancel"
                );
                
                if (confirmed) {
                    try {
                        const response = await fetch(`../learnnest-backend/api/delete-course.php?id=${courseId}`, {
                            method: "DELETE"
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showToast(`✅ Course deleted successfully`);
                            loadCourses();
                        } else {
                            showToast(`❌ ${result.message}`, "danger");
                        }
                    } catch (error) {
                        showToast(`❌ Error: ${error.message}`, "danger");
                    }
                }
            }
        }
    });
    
    // Edit form submission
    document.getElementById("editCourseForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        
        const formData = {
            id: document.getElementById("editCourseId").value,
            name: document.getElementById("editCourseName").value,
            description: document.getElementById("editDescription").value,
            duration: document.getElementById("editDuration").value,
            price: document.getElementById("editPrice").value,
            students: document.getElementById("editStudents").value,
            status: document.getElementById("editStatus").value
        };
        
        try {
            const response = await fetch("../learnnest-backend/api/update-course.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(`✅ Course updated successfully`);
                bootstrap.Modal.getInstance(document.getElementById("editCourseModal")).hide();
                loadCourses();
            } else {
                const errorMsg = result.error ? result.message + ": " + result.error : result.message;
                showToast(`❌ ${errorMsg}`, "danger");
            }
        } catch (error) {
            showToast(`❌ Error: ${error.message}`, "danger");
        }
    });
});

// Toast notification
function showToast(message, type = "success") {
    const toastEl = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");
    
    toastMessage.textContent = message;
    toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Confirmation dialog
function showConfirmDialog(title, message, confirmText, cancelText) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">${cancelText}</button>
                        <button type="button" class="btn btn-danger" id="confirmBtn">${confirmText}</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const instance = new bootstrap.Modal(modal);
        instance.show();
        
        modal.querySelector('#confirmBtn').addEventListener('click', () => {
            instance.hide();
            setTimeout(() => document.body.removeChild(modal), 300);
            resolve(true);
        });
        
        modal.querySelector('#cancelBtn').addEventListener('click', () => {
            instance.hide();
            setTimeout(() => document.body.removeChild(modal), 300);
            resolve(false);
        });
    });
}

// Toggle sidebar
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("open");
}