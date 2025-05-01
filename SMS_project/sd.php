<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../config.php');


// Get student ID from URL or post data
$student_id = $_GET['student_id'];

// Set the response content type to JSON
header('Content-Type: application/json');

// Fetch student info
$student_sql = "SELECT * FROM students WHERE student_id = ?";
$student_stmt = $connect->prepare($student_sql);
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();

// Fetch attendance records
$attendance_sql = "SELECT * FROM attendances WHERE student_id = ?";
$attendance_stmt = $connect->prepare($attendance_sql);
$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance = array();
while ($row = $attendance_result->fetch_assoc()) {
    $attendance[] = $row;
}

// Fetch marks/grades
$marks_sql = "SELECT * FROM marks WHERE student_id = ?";
$marks_stmt = $connect->prepare($marks_sql);
$marks_stmt->bind_param("i", $student_id);
$marks_stmt->execute();
$marks_result = $marks_stmt->get_result();
$marks = array();
while ($row = $marks_result->fetch_assoc()) {
    $marks[] = $row;
}

// Example of recent activity (you may need a table or custom logic)
$recent_activity = array(
    'Submitted Assignment 3',
    'Issued Book: Science Vol 2',
    'Marked present on 24 April'
);

// Compile all data into one response
$response = array(
    'student_info' => $student,
    'attendance' => $attendance,
    'marks' => $marks,
    'recent_activity' => $recent_activity
);

// Return the response as JSON
echo json_encode($response);

// Close the connection
$conn->close();
?>
