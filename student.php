<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Student Management | David Elementary University</title>
    <!-- Bootstrap 4 CSS (CDN fallback + local path mimic but fully functional) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .uni-header {
            background: linear-gradient(135deg, #0b2b40 0%, #154e6b 100%);
            color: white;
            padding: 1.8rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .uni-header h1 {
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
            font-size: 2rem;
        }
        .uni-header .motto {
            margin-top: 8px;
            font-size: 0.9rem;
            opacity: 0.9;
            letter-spacing: 1px;
        }
        .nav-links {
            background: #ffffff;
            padding: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e9ecef;
        }
        .nav-links .nav-link {
            color: #2c3e4e;
            padding: 0.9rem 1.6rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-bottom: 3px solid transparent;
            display: inline-block;
        }
        .nav-links .nav-link:hover {
            background: #f8f9fa;
            color: #1f6e8c;
            border-bottom-color: #ffb347;
            text-decoration: none;
        }
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
        }
        .form-card .card-header {
            background: #fef9ef;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
            color: #1f5068;
        }
        .form-card .card-header i {
            margin-right: 8px;
            color: #ff8c42;
        }
        .form-control, .form-control:focus {
            border-radius: 12px;
            padding: 0.6rem 1rem;
            border: 1px solid #cfdee9;
            transition: 0.2s;
        }
        .form-control:focus {
            border-color: #ffb347;
            box-shadow: 0 0 0 3px rgba(255,180,71,0.2);
        }
        .btn-send {
            background: #1f6e8c;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            font-weight: 500;
            transition: all 0.2s;
            color: white;
        }
        .btn-send:hover {
            background: #0e4e66;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .btn-reset {
            background: #e9ecef;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            color: #4a6272;
            font-weight: 500;
        }
        .btn-reset:hover {
            background: #dee2e6;
        }
        .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            overflow-x: auto;
        }
        .table-card .card-header {
            background: #fef9ef;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1rem 1.5rem;
        }
        .table thead th {
            background: #eef3fa;
            color: #1f5068;
            font-weight: 600;
            border-bottom: 2px solid #d4e0e9;
            padding: 1rem 0.8rem;
            font-size: 0.9rem;
        }
        .table tbody td {
            vertical-align: middle;
            padding: 0.8rem;
            color: #2c3e4e;
        }
        .table-hover tbody tr:hover {
            background-color: #fef5e9;
        }
        .btn-action {
            border-radius: 30px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 500;
            margin: 0 2px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-delete {
            background: #ffe6e5;
            color: #c13b2b;
            border: 1px solid #ffcdc9;
        }
        .btn-delete:hover {
            background: #f8d7da;
            color: #a71d2a;
            text-decoration: none;
        }
        .btn-update {
            background: #e3f0fa;
            color: #1f6e8c;
            border: 1px solid #cde1ef;
        }
        .btn-update:hover {
            background: #cde1f0;
            color: #0a4b64;
            text-decoration: none;
        }
        footer {
            background: #eef2f5;
            margin-top: 3rem;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #5b7c8e;
            border-top: 1px solid #dce5ec;
        }
        @media (max-width: 768px) {
            .nav-links .nav-link {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }
            .uni-header h1 {
                font-size: 1.4rem;
            }
        }
        /* modal styling for update */
        .modal-custom .modal-content {
            border-radius: 24px;
            border: none;
        }
        .modal-custom .modal-header {
            background: #fef9ef;
            border-bottom: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<!-- ======================= -->
<!-- STATIC UI: SAME LAYOUT AND MEANING, BUT WITH LOCALSTORAGE JS LOGIC (NO PHP/BACKEND) -->
<!-- Preserves the original university name, navigation, forms, table, and actions -->
<!-- ======================= -->

<div class="container-fluid px-0">
    <!-- HEADER -->
    <div class="uni-header text-center">
        <div class="container">
            <h1>DAVID ELEMENTARY UNIVERSITY</h1>
            <div class="motto">"SUCCESS · INTEGRITY · EXCELLENCE"</div>
        </div>
    </div>

    <!-- NAVIGATION (preserved all links, they point to same pages but are just placeholders for frontend demo) -->
    <div class="nav-links text-center">
        <div class="container">
            <div class="nav justify-content-center">
                <a class="nav-link" href="home.php"><i class="fas fa-user-graduate"></i> Dashboard</a>
                <a class="nav-link" href="student.php" class="active"><i class="fas fa-user-graduate"></i> Student</a>
                <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                <a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                <a class="nav-link" href="department.php"><i class="fas fa-building"></i> Department</a>
                <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i> Courseunit</a>
                <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT: Form + Table -->
    <div class="container mt-5 mb-4">
        <div class="row">
            <!-- LEFT: Registration Form (exactly same fields as original) -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-pen-alt"></i> Student Registration
                    </div>
                    <div class="card-body p-4">
                        <!-- No method POST, we use JS to handle localStorage -->
                        <form id="studentForm">
                            <div class="form-group mb-3">
                                <label for="regno" class="form-label fw-semibold">Registration Number</label>
                                <input type="text" class="form-control" id="regno" name="regno" placeholder="e.g., 2024/CS/001" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="firstname" class="form-label fw-semibold">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="lastname" class="form-label fw-semibold">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contact" class="form-label fw-semibold">Contact Number</label>
                                <input type="number" class="form-control" id="contact" name="contact" placeholder="Phone number" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="course" class="form-label fw-semibold">Course</label>
                                <input type="text" class="form-control" id="course" name="course" placeholder="e.g., Computer Science" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="department" class="form-label fw-semibold">Department</label>
                                <input type="text" class="form-control" id="department" name="department" placeholder="e.g., Computing & Informatics" required>
                            </div>
                            <div class="d-flex gap-3 justify-content-start">
                                <button type="submit" id="sendBtn" class="btn btn-send"><i class="fas fa-save me-1"></i> Register Student</button>
                                <button type="button" id="resetFormBtn" class="btn btn-reset"><i class="fas fa-undo-alt me-1"></i> Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Student List Table -->
            <div class="col-lg-8 col-md-12">
                <div class="table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-table-list me-2"></i> Enrolled Students</span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">Updated in real-time</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="studentTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Reg No</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Contact</th>
                                    <th>Course</th>
                                    <th>Department</th>
                                    <th colspan="2" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <!-- dynamic rows injected via JS -->
                                <tr id="noDataRow">
                                    <td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-database me-2"></i>No students registered yet. Use the form to add.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UPDATE MODAL (for in-place editing without changing original meaning) -->
    <div class="modal fade modal-custom" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel"><i class="fas fa-user-edit"></i> Update Student Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateForm">
                        <input type="hidden" id="updateId">
                        <div class="form-group mb-3">
                            <label>Registration Number</label>
                            <input type="text" class="form-control" id="updateRegno" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" id="updateFirstname" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Last Name</label>
                            <input type="text" class="form-control" id="updateLastname" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Contact Number</label>
                            <input type="number" class="form-control" id="updateContact" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Course</label>
                            <input type="text" class="form-control" id="updateCourse" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Department</label>
                            <input type="text" class="form-control" id="updateDepartment" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-reset" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-send" id="confirmUpdateBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Empowering future leaders
            <br><small>© <span id="currentYear"></span> | Student Information System</small>
        </div>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ========================
    //  Complete LocalStorage Student Management System
    //  Replaces PHP backend but keeps exact same UI, meaning and flow
    //  Includes: CREATE, READ, UPDATE, DELETE, realtime table render
    // ========================

    // Storage key
    const STORAGE_KEY = "david_university_students";

    // Load students from localStorage or initialize with default demo data (optional empty)
    let students = [];

    function loadStudentsFromStorage() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            students = JSON.parse(stored);
        } else {
            // No default seed, start empty as per original meaning (but we could add one sample for demonstration? keep clean)
            students = [];
            // If you want a demo student to show table not empty initially, uncomment below, but better to keep as empty.
            // but to align with original "No students registered yet" behavior, we keep empty.
            students = [];
        }
    }

    // Save current students array to localStorage
    function saveStudentsToStorage() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(students));
    }

    // Helper to generate next ID based on current max id (increment)
    function getNextId() {
        if (students.length === 0) return 1;
        const maxId = Math.max(...students.map(s => s.id));
        return maxId + 1;
    }

    // Render the table dynamically based on students array
    function renderStudentTable() {
        const tbody = document.getElementById('studentTableBody');
        if (!tbody) return;
        
        if (students.length === 0) {
            tbody.innerHTML = `<tr id="noDataRow"><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-database me-2"></i>No students registered yet. Use the form to add.</td></tr>`;
            return;
        }
        
        let html = '';
        students.forEach(student => {
            html += `
                <tr data-id="${student.id}">
                    <td class="fw-semibold">${student.id}</td>
                    <td>${escapeHtml(student.regno)}</td>
                    <td>${escapeHtml(student.firstname)}</td>
                    <td>${escapeHtml(student.lastname)}</td>
                    <td>${escapeHtml(student.contact)}</td>
                    <td>${escapeHtml(student.course)}</td>
                    <td>${escapeHtml(student.department)}</td>
                    <td class="text-center">
                        <button class="btn btn-action btn-delete delete-student" data-id="${student.id}">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-action btn-update update-student" data-id="${student.id}">
                            <i class="fas fa-edit me-1"></i> Update
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
        
        // Attach delete events after render
        document.querySelectorAll('.delete-student').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = parseInt(btn.getAttribute('data-id'));
                if (confirm('Are you sure you want to delete this student?')) {
                    deleteStudentById(id);
                }
            });
        });
        
        // Attach update events
        document.querySelectorAll('.update-student').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = parseInt(btn.getAttribute('data-id'));
                openUpdateModal(id);
            });
        });
    }
    
    // Simple XSS escape helper
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
            return c;
        });
    }
    
    // Delete student
    function deleteStudentById(id) {
        students = students.filter(student => student.id !== id);
        saveStudentsToStorage();
        renderStudentTable();
        showToast('Student deleted successfully', 'success');
    }
    
    // Add new student
    function addStudent(studentData) {
        // Check for duplicate registration number?
        const exists = students.some(s => s.regno.toLowerCase() === studentData.regno.toLowerCase());
        if (exists) {
            showToast('Registration number already exists! Please use a unique Reg No.', 'error');
            return false;
        }
        const newStudent = {
            id: getNextId(),
            regno: studentData.regno.trim(),
            firstname: studentData.firstname.trim(),
            lastname: studentData.lastname.trim(),
            contact: studentData.contact.trim(),
            course: studentData.course.trim(),
            department: studentData.department.trim()
        };
        students.push(newStudent);
        saveStudentsToStorage();
        renderStudentTable();
        return true;
    }
    
    // Update existing student
    function updateStudentById(id, updatedData) {
        const index = students.findIndex(s => s.id === id);
        if (index !== -1) {
            // Check duplicate regno (excluding current)
            const duplicate = students.some(s => s.id !== id && s.regno.toLowerCase() === updatedData.regno.toLowerCase());
            if (duplicate) {
                showToast('Registration number already used by another student!', 'error');
                return false;
            }
            students[index] = {
                ...students[index],
                regno: updatedData.regno.trim(),
                firstname: updatedData.firstname.trim(),
                lastname: updatedData.lastname.trim(),
                contact: updatedData.contact.trim(),
                course: updatedData.course.trim(),
                department: updatedData.department.trim()
            };
            saveStudentsToStorage();
            renderStudentTable();
            return true;
        }
        return false;
    }
    
    // Open modal with student data for update
    function openUpdateModal(id) {
        const student = students.find(s => s.id === id);
        if (student) {
            document.getElementById('updateId').value = student.id;
            document.getElementById('updateRegno').value = student.regno;
            document.getElementById('updateFirstname').value = student.firstname;
            document.getElementById('updateLastname').value = student.lastname;
            document.getElementById('updateContact').value = student.contact;
            document.getElementById('updateCourse').value = student.course;
            document.getElementById('updateDepartment').value = student.department;
            $('#updateModal').modal('show');
        }
    }
    
    // Simple toast notif (alert replacement but more modern, but uses alert fallback, but we use nicer message)
    function showToast(message, type = 'success') {
        // Preserve similar alert behavior as original but with better UI, but we can use alert for consistency
        // However original used alert, but to keep professional we can still alert? but alert is fine but we prefer non-intrusive? keep alert as original style?
        // Original code had alert for success/error. I'll replicate exact user experience: alert for feedback.
        if (type === 'success') {
            alert("✅ " + message);
        } else {
            alert("❌ " + message);
        }
    }
    
    // Reset registration form fields
    function resetRegistrationForm() {
        document.getElementById('studentForm').reset();
    }
    
    // Event listener for registration form
    document.getElementById('studentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const regno = document.getElementById('regno').value;
        const firstname = document.getElementById('firstname').value;
        const lastname = document.getElementById('lastname').value;
        const contact = document.getElementById('contact').value;
        const course = document.getElementById('course').value;
        const department = document.getElementById('department').value;
        
        if (!regno || !firstname || !lastname || !contact || !course || !department) {
            alert("❌ Please fill all fields before registering.");
            return;
        }
        
        const newStudent = { regno, firstname, lastname, contact, course, department };
        const success = addStudent(newStudent);
        if (success) {
            resetRegistrationForm();
            showToast('Registered Successfully', 'success');
        }
    });
    
    // Reset button functionality (clear fields)
    document.getElementById('resetFormBtn').addEventListener('click', function() {
        resetRegistrationForm();
    });
    
    // Confirm update button inside modal
    document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
        const id = parseInt(document.getElementById('updateId').value);
        const regno = document.getElementById('updateRegno').value;
        const firstname = document.getElementById('updateFirstname').value;
        const lastname = document.getElementById('updateLastname').value;
        const contact = document.getElementById('updateContact').value;
        const course = document.getElementById('updateCourse').value;
        const department = document.getElementById('updateDepartment').value;
        
        if (!regno || !firstname || !lastname || !contact || !course || !department) {
            alert("❌ All fields are required for update.");
            return;
        }
        
        const updated = { regno, firstname, lastname, contact, course, department };
        const success = updateStudentById(id, updated);
        if (success) {
            $('#updateModal').modal('hide');
            showToast('Student updated successfully', 'success');
        }
    });
    
    // Set current year in footer
    document.getElementById('currentYear').innerText = new Date().getFullYear();
    
    // Initialize: load data, render table, handle any additional styling
    loadStudentsFromStorage();
    renderStudentTable();
    
    // additional: clear modal data on close
    $('#updateModal').on('hidden.bs.modal', function () {
        document.getElementById('updateForm').reset();
    });
    
    // preserve responsiveness and any active class for navigation
    // Also you can set active tab if needed
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links .nav-link');
    navLinks.forEach(link => {
        if(link.innerText.includes('Student')) {
            link.style.borderBottomColor = '#ffb347';
            link.style.color = '#1f6e8c';
        }
    });
    
    // Make sure that when delete/update, all events are captured after render 
    // (No extra server side, everything works offline, all same functionalities)
    // The table shows real-time data stored in localStorage.
    
    // If we need to emulate "delete_student.php" and "update_student.php" but we replaced via modal + delete inline
    // The original intent: save & display on screen without changing meaning, now fully functional client-side.
    console.log("Student Management System Ready — Data persists in browser localStorage");
</script>
</body>
</html>
```