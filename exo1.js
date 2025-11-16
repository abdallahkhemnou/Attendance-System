$(document).ready(function() {
    // Initial student data
    const initialStudents = [
        {
            lastName: "Ahmed",
            firstName: "Sara",
            attendance: [true, true, false, false, false, false],
            participation: [true, false, false, false, false, false]
        },
        {
            lastName: "Yacine",
            firstName: "Ali",
            attendance: [true, false, true, true, true, true],
            participation: [false, true, true, true, true, true]
        },
        {
            lastName: "Houcine",
            firstName: "Rania",
            attendance: [true, true, false, true, true, false],
            participation: [true, false, true, false, false, false]
        }
    ];

    let students = [...initialStudents];
    let currentSort = '';

    // Initialize table with initial data
    initializeTable();

    // Exercise 2 & 3: Form validation and submission
    $('#addStudentForm').on('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            addStudent();
        }
    });

    // Exercise 4: Show report
    $('#showReportBtn').click(showReport);

    // Exercise 5: Table interactions
    $('#attendanceTable tbody').on('mouseenter', 'tr', function() {
        $(this).addClass('highlighted');
    }).on('mouseleave', 'tr', function() {
        $(this).removeClass('highlighted');
    });

    $('#attendanceTable tbody').on('click', 'tr', function() {
        const index = $(this).index();
        const student = students[index];
        const absences = calculateAbsences(student);
        alert(`Student: ${student.firstName} ${student.lastName}\nAbsences: ${absences}`);
    });

    // Exercise 6: Highlight excellent students
    $('#highlightExcellentBtn').click(highlightExcellentStudents);
    $('#resetColorsBtn').click(resetColors);

    // Exercise 7: Search and Sort
    $('#searchInput').on('input', filterTable);
    $('#sortAbsencesAsc').click(() => sortTable('absences', 'asc'));
    $('#sortParticipationDesc').click(() => sortTable('participation', 'desc'));

    function initializeTable() {
        const tbody = $('#attendanceTable tbody');
        tbody.empty();
        
        students.forEach(student => {
            const absences = calculateAbsences(student);
            const participation = calculateParticipation(student);
            const message = generateMessage(absences, participation);
            
            const row = $('<tr>').addClass(getAttendanceClass(absences));
            
            row.append(`<td>${student.lastName}</td>`);
            row.append(`<td>${student.firstName}</td>`);
            
            // Add attendance and participation cells
            for (let i = 0; i < 6; i++) {
                const attCell = student.attendance[i] ? '✓' : '';
                const partCell = student.participation[i] ? '✓' : '';
                row.append(`<td>${attCell}</td><td>${partCell}</td>`);
            }
            
            row.append(`<td>${absences}</td>`);
            row.append(`<td>${participation}</td>`);
            row.append(`<td>${message}</td>`);
            
            tbody.append(row);
        });
    }

    function calculateAbsences(student) {
        return student.attendance.filter(present => !present).length;
    }

    function calculateParticipation(student) {
        return student.participation.filter(participated => participated).length;
    }

    function getAttendanceClass(absences) {
        if (absences < 3) return 'good-attendance';
        if (absences <= 4) return 'warning-attendance';
        return 'bad-attendance';
    }

    function generateMessage(absences, participation) {
        if (absences < 3 && participation >= 4) {
            return "Good attendance – Excellent participation";
        } else if (absences >= 5) {
            return "Excluded – too many absences – You need to participate more";
        } else if (absences >= 3 || participation < 3) {
            return "Warning – attendance low – You need to participate more";
        } else {
            return "Satisfactory";
        }
    }

    // Exercise 2: Form validation
    function validateForm() {
        let isValid = true;

        // Student ID validation
        const studentId = $('#studentId').val();
        const studentIdError = $('#studentIdError');
        if (!studentId) {
            studentIdError.text('Student ID is required');
            isValid = false;
        } else if (!/^\d+$/.test(studentId)) {
            studentIdError.text('Student ID must contain only numbers');
            isValid = false;
        } else {
            studentIdError.text('');
        }

        // Last Name validation
        const lastName = $('#lastName').val();
        const lastNameError = $('#lastNameError');
        if (!lastName) {
            lastNameError.text('Last Name is required');
            isValid = false;
        } else if (!/^[A-Za-z]+$/.test(lastName)) {
            lastNameError.text('Last Name must contain only letters');
            isValid = false;
        } else {
            lastNameError.text('');
        }

        // First Name validation
        const firstName = $('#firstName').val();
        const firstNameError = $('#firstNameError');
        if (!firstName) {
            firstNameError.text('First Name is required');
            isValid = false;
        } else if (!/^[A-Za-z]+$/.test(firstName)) {
            firstNameError.text('First Name must contain only letters');
            isValid = false;
        } else {
            firstNameError.text('');
        }

        // Email validation
        const email = $('#email').val();
        const emailError = $('#emailError');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            emailError.text('Email is required');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            emailError.text('Please enter a valid email address');
            isValid = false;
        } else {
            emailError.text('');
        }

        return isValid;
    }

    // Exercise 3: Add student to table
    function addStudent() {
        const newStudent = {
            lastName: $('#lastName').val(),
            firstName: $('#firstName').val(),
            attendance: [false, false, false, false, false, false],
            participation: [false, false, false, false, false, false]
        };

        students.push(newStudent);
        initializeTable();
        
        // Show confirmation message
        $('#confirmationMessage').text(`Student ${newStudent.firstName} ${newStudent.lastName} added successfully!`);
        
        // Clear form
        $('#addStudentForm')[0].reset();
        
        // Hide confirmation after 3 seconds
        setTimeout(() => {
            $('#confirmationMessage').text('');
        }, 3000);
    }

    // Exercise 4: Show report
    function showReport() {
        const totalStudents = students.length;
        const presentStudents = students.filter(student => 
            student.attendance.some(present => present)
        ).length;
        const participatingStudents = students.filter(student => 
            student.participation.some(participated => participated)
        ).length;

        $('#reportContent').html(`
            <p>Total Students: ${totalStudents}</p>
            <p>Students Present (at least once): ${presentStudents}</p>
            <p>Students Participated (at least once): ${participatingStudents}</p>
        `);

        // Create simple bar chart
        const chartHtml = `
            <div class="bar-container">
                <div class="bar" style="height: ${(totalStudents/totalStudents)*100}%">
                    <div class="bar-label">Total<br>${totalStudents}</div>
                </div>
                <div class="bar" style="height: ${(presentStudents/totalStudents)*100}%">
                    <div class="bar-label">Present<br>${presentStudents}</div>
                </div>
                <div class="bar" style="height: ${(participatingStudents/totalStudents)*100}%">
                    <div class="bar-label">Participated<br>${participatingStudents}</div>
                </div>
            </div>
        `;
        $('#chartContainer').html(chartHtml);

        $('#reportSection').show();
    }

    // Exercise 6: Highlight excellent students
    function highlightExcellentStudents() {
        $('#attendanceTable tbody tr').each(function(index) {
            const student = students[index];
            const absences = calculateAbsences(student);
            
            if (absences < 3) {
                $(this).addClass('highlighted');
                $(this).fadeOut(500).fadeIn(500);
            }
        });
    }

    function resetColors() {
        $('#attendanceTable tbody tr').each(function(index) {
            const student = students[index];
            const absences = calculateAbsences(student);
            $(this).removeClass('highlighted');
            $(this).attr('class', getAttendanceClass(absences));
        });
    }

    // Exercise 7: Search and Sort
    function filterTable() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        
        $('#attendanceTable tbody tr').each(function(index) {
            const student = students[index];
            const fullName = (student.firstName + ' ' + student.lastName).toLowerCase();
            const showRow = fullName.includes(searchTerm);
            $(this).toggle(showRow);
        });
    }

    function sortTable(criteria, order) {
        students.sort((a, b) => {
            let aValue, bValue;
            
            if (criteria === 'absences') {
                aValue = calculateAbsences(a);
                bValue = calculateAbsences(b);
            } else if (criteria === 'participation') {
                aValue = calculateParticipation(a);
                bValue = calculateParticipation(b);
            }
            
            if (order === 'asc') {
                return aValue - bValue;
            } else {
                return bValue - aValue;
            }
        });

        currentSort = `Currently sorted by ${criteria} (${order === 'asc' ? 'ascending' : 'descending'})`;
        $('#sortInfo').text(currentSort);
        
        initializeTable();
    }
});