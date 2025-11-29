<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $group = trim($_POST['group']);
    
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
    if (empty($errors)) {
        $students = [];
        if (file_exists('students.json')) {
            $json_data = file_get_contents('students.json');
            $students = json_decode($json_data, true) ?: [];
        }
        foreach ($students as $student) {
            if ($student['student_id'] === $student_id) {
                $errors[] = "Student ID already exists";
                break;
            }
        }
        
        if (empty($errors)) {
            $new_student = [
                'student_id' => $student_id,
                'name' => $name,
                'group' => $group
            ];
            
            $students[] = $new_student;
            
            if (file_put_contents('students.json', json_encode($students, JSON_PRETTY_PRINT))) {
                header('Location: add_student.php?success=true&message=Student added successfully!');
                exit;
            } else {
                $errors[] = "Error saving student data";
            }
        }
    }
    
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