<?php
// Get date from query parameter or use today's date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$attendance_file = "attendance_{$date}.json";

// Load attendance data if exists
$attendance_data = [];
if (file_exists($attendance_file)) {
    $json_data = file_get_contents($attendance_file);
    $attendance_data = json_decode($json_data, true) ?: [];
}

// Load students for reference
$students = [];
if (file_exists('students.json')) {
    $json_data = file_get_contents('students.json');
    $students = json_decode($json_data, true) ?: [];
}

// Create student lookup array
$student_lookup = [];
foreach ($students as $student) {
    $student_lookup[$student['student_id']] = $student;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .date-selector { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .empty { text-align: center; padding: 20px; color: #6c757d; }
        .status-present { color: #28a745; font-weight: bold; }
        .status-absent { color: #dc3545; font-weight: bold; }
        .status-late { color: #ffc107; font-weight: bold; }
        .status-excused { color: #6c757d; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attendance Records</h1>
        
        <div class="date-selector">
            <form method="GET">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" max="<?php echo date('Y-m-d'); ?>">
                <button type="submit">View Attendance</button>
            </form>
        </div>
        
        <h2>Attendance for <?php echo date('F j, Y', strtotime($date)); ?></h2>
        
        <?php if (empty($attendance_data)): ?>
            <div class="empty">No attendance records found for this date.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Group</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_data as $record): 
                        $student = isset($student_lookup[$record['student_id']]) ? $student_lookup[$record['student_id']] : null;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                        <td><?php echo $student ? htmlspecialchars($student['name']) : 'Unknown Student'; ?></td>
                        <td><?php echo $student ? htmlspecialchars($student['group']) : 'N/A'; ?></td>
                        <td class="status-<?php echo htmlspecialchars($record['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($record['status'])); ?>
                        </td>
                        <td><?php echo htmlspecialchars($record['timestamp']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php
            // Calculate statistics
            $total = count($attendance_data);
            $present = count(array_filter($attendance_data, function($record) {
                return $record['status'] === 'present';
            }));
            $absent = count(array_filter($attendance_data, function($record) {
                return $record['status'] === 'absent';
            }));
            $attendance_rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            ?>
            
            <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <h3>Statistics</h3>
                <p>Total Students: <?php echo $total; ?></p>
                <p>Present: <?php echo $present; ?></p>
                <p>Absent: <?php echo $absent; ?></p>
                <p>Attendance Rate: <?php echo $attendance_rate; ?>%</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="take_attendance.php">Take Attendance</a> | 
            <a href="add_student.php">Add New Student</a>
        </div>
    </div>
</body>
</html>