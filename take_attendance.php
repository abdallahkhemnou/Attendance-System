<?php

$today = date('Y-m-d');
$attendance_file = "attendance_{$today}.json";
$attendance_taken = file_exists($attendance_file);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$attendance_taken) {
    $attendance_data = [];
    
    foreach ($_POST['attendance'] as $student_id => $status) {
        $attendance_data[] = [
            'student_id' => $student_id,
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    if (file_put_contents($attendance_file, json_encode($attendance_data, JSON_PRETTY_PRINT))) {
        $attendance_taken = true;
        $message = "Attendance for {$today} saved successfully!";
    } else {
        $message = "Error saving attendance data!";
    }
}
$students = [];
if (file_exists('students.json')) {
    $json_data = file_get_contents('students.json');
    $students = json_decode($json_data, true) ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .attendance-form { margin-top: 20px; }
        button { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        button:disabled { background: #6c757d; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Take Attendance - <?php echo date('F j, Y'); ?></h1>
        
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($attendance_taken): ?>
            <div class="message warning">
                Attendance for today has already been taken.
                <div style="margin-top: 10px;">
                    <a href="view_attendance.php?date=<?php echo $today; ?>">View Today's Attendance</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (empty($students)): ?>
            <div class="message warning">
                No students found. <a href="add_student.php">Add students first</a>.
            </div>
        <?php else: ?>
            <form method="POST" class="attendance-form">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['group']); ?></td>
                            <td>
                                <select name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>]" <?php echo $attendance_taken ? 'disabled' : ''; ?>>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" <?php echo $attendance_taken ? 'disabled' : ''; ?>>
                    <?php echo $attendance_taken ? 'Attendance Already Taken' : 'Save Attendance'; ?>
                </button>
            </form>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="add_student.php">Add New Student</a> | 
            <a href="view_attendance.php">View All Attendance</a>
        </div>
    </div>
</body>
</html>