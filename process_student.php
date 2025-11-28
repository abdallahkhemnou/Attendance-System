<?php
// Process student form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $group = trim($_POST['group']);
    
    // Validate data
    $errors = [];
    
    if (empty($student_id)) {
        $errors[] = "Student ID is required";
    } elseif (!preg_match('/^\d+$/', $student_id)) {
        $errors[] = "Student ID must contain only numbers";
    }
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($group)) {
        $errors[] = "Group is required";
    }
    
    // If no errors, process the data
    if (empty($errors)) {
        // Load existing students
        $students = [];
        if (file_exists('students.json')) {
            $json_data = file_get_contents('students.json');
            $students = json_decode($json_data, true) ?: [];
        }
        
        // Check if student ID already exists
        foreach ($students as $student) {
            if ($student['student_id'] === $student_id) {
                $errors[] = "Student ID already exists";
                break;
            }
        }
        
        if (empty($errors)) {
            // Add new student
            $new_student = [
                'student_id' => $student_id,
                'name' => $name,
                'group' => $group
            ];
            
            $students[] = $new_student;
            
            // Save back to JSON file
            if (file_put_contents('students.json', json_encode($students, JSON_PRETTY_PRINT))) {
                header('Location: add_student.php?success=true&message=Student added successfully!');
                exit;
            } else {
                $errors[] = "Error saving student data";
            }
        }
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_message = implode(', ', $errors);
        header('Location: add_student.php?success=false&message=' . urlencode($error_message));
        exit;
    }
} else {
    header('Location: add_student.php');
    exit;
}
?>