$(document).ready(function () {

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

    rebuildTable();

    // ================================
    // BUTTONS (delegated)
    // ================================
    $(document).on("click", "#sortAbsencesAsc", () => {
        clearHighlights();
        students.sort((a, b) => calculateAbsences(a) - calculateAbsences(b));
        rebuildTable();
    });

    $(document).on("click", "#sortParticipationDesc", () => {
        clearHighlights();
        students.sort((a, b) => calculateParticipation(b) - calculateParticipation(a));
        rebuildTable();
    });

    $(document).on("click", "#highlightExcellentBtn", () => {
        clearHighlights();
        highlightExcellent();
    });

    $(document).on("click", "#resetColorsBtn", () => {
        clearHighlights();
    });

    $(document).on("click", "#showReportBtn", () => {
        $("#reportSection").toggle();
        showReport();
    });

    // SEARCH
    $(document).on("input", "#searchInput", filterTable);

    // ADD STUDENT
    $(document).on("submit", "#addStudentForm", function (e) {
        e.preventDefault();
        addStudent();
    });

    // ================================
    // TOGGLE ATTENDANCE/PARTICIPATION
    // ================================
    $(document).on("click", ".attendance-cell", function () {
        let s = $(this).data("s");
        let i = $(this).data("i");

        students[s].attendance[i] = !students[s].attendance[i];
        rebuildTable();
    });

    $(document).on("click", ".participation-cell", function () {
        let s = $(this).data("s");
        let i = $(this).data("i");

        students[s].participation[i] = !students[s].participation[i];
        rebuildTable();
    });

    // ================================
    // BUILD TABLE
    // ================================
    function rebuildTable() {
        let tbody = $("#attendanceTable tbody");
        tbody.empty();

        students.forEach((student, index) => {
            let abs = calculateAbsences(student);
            let part = calculateParticipation(student);

            let tr = $("<tr>").addClass(getAttendanceClass(abs));

            tr.append(`<td>${student.lastName}</td>`);
            tr.append(`<td>${student.firstName}</td>`);

            for (let i = 0; i < 6; i++) {
                tr.append(`<td class="attendance-cell" data-s="${index}" data-i="${i}">${student.attendance[i] ? "✓" : ""}</td>`);
                tr.append(`<td class="participation-cell" data-s="${index}" data-i="${i}">${student.participation[i] ? "✓" : ""}</td>`);
            }

            tr.append(`<td>${abs}</td>`);
            tr.append(`<td>${part}</td>`);
            tr.append(`<td>${generateMessage(abs, part)}</td>`);

            tbody.append(tr);
        });

        filterTable(); // keep search active after sorting
    }

    // ================================
    // SEARCH FIXED (NO MORE INDEX BUG)
    // ================================
    function filterTable() {
        const txt = $("#searchInput").val().toLowerCase();

        $("#attendanceTable tbody tr").each(function () {
            const name =
                $(this).find("td:nth-child(1)").text().toLowerCase() + " " +
                $(this).find("td:nth-child(2)").text().toLowerCase();

            $(this).toggle(name.includes(txt));
        });
    }

    // ================================
    // HIGHLIGHT
    // ================================
    function highlightExcellent() {
        $("#attendanceTable tbody tr").each(function (rowIndex) {
            let student = students[rowIndex];
            if (calculateAbsences(student) < 3) $(this).addClass("highlighted");
        });
    }

    function clearHighlights() {
        $("#attendanceTable tbody tr").removeClass("highlighted");
    }

    // ================================
    // REPORT
    // ================================
    function showReport() {
        $("#reportContent").html(`
            Total Students: ${students.length}<br>
            Present at least once: ${students.filter(s => s.attendance.some(a => a)).length}<br>
            Participated at least once: ${students.filter(s => s.participation.some(a => a)).length}
        `);

        drawReportChart();
    }

    function drawReportChart() {
        const container = $("#chartContainer");
        container.empty();

        students.forEach(s => {
            const abs = calculateAbsences(s);
            container.append(`<div class="bar" style="height:${abs * 30}px;"></div>`);
        });
    }

    // ================================
    // ADD STUDENT
    // ================================
    function addStudent() {
        let ln = $("#lastName").val();
        let fn = $("#firstName").val();

        if (!ln || !fn) return;

        students.push({
            lastName: ln,
            firstName: fn,
            attendance: [false, false, false, false, false, false],
            participation: [false, false, false, false, false, false]
        });

        rebuildTable();
    }

    // ================================
    // HELPERS
    // ================================
    function calculateAbsences(s) {
        return s.attendance.filter(a => !a).length;
    }

    function calculateParticipation(s) {
        return s.participation.filter(a => a).length;
    }

    function getAttendanceClass(abs) {
        if (abs < 3) return "good-attendance";
        if (abs <= 4) return "warning-attendance";
        return "bad-attendance";
    }

    function generateMessage(abs, part) {
        if (abs >= 5) return "Excluded – too many absences";
        if (abs >= 3 || part < 3) return "Warning – low participation";
        if (abs < 3 && part >= 4) return "Excellent";
        return "OK";
    }

});
