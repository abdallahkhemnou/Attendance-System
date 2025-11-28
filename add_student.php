<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { margin-top: 15px; padding: 10px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Student</h1>
        
        <?php
        // Display messages from form processing
        if (isset($_GET['message'])) {
            $messageType = isset($_GET['success']) && $_GET['success'] == 'true' ? 'success' : 'error';
            echo '<div class="message ' . $messageType . '">' . htmlspecialchars($_GET['message']) . '</div>';
        }
        ?>
        
        <form method="POST" action="process_student.php">
            <div class="form-group">
                <label for="student_id">Student ID:</label>
                <input type="text" id="student_id" name="student_id" required>
            </div>
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="group">Group:</label>
                <input type="text" id="group" name="group" required>
            </div>
            <button type="submit">Add Student</button>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="take_attendance.php">Take Attendance</a> | 
            <a href="view_students.php">View All Students</a>
        </div>
    </div>
</body>
</html>