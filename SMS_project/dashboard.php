<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../config.php');

$response = [];

$studentQuery = $connect->query("SELECT COUNT(*) as student_count FROM students");
$teacherQuery = $connect->query("SELECT COUNT(*) as teacher_count FROM teachers");

$response['students'] = $studentQuery->fetch_assoc()['student_count'];
$response['teachers'] = $teacherQuery->fetch_assoc()['teacher_count'];

$query = "
    SELECT 
        DAYNAME(date) AS day, 
        COUNT(*) AS student_count
    FROM attendances 
    WHERE status = 'present'
    GROUP BY DAYNAME(date)
";
$result = $connect->query($query);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'day' => $row['day'],
            'students' => $row['student_count'],
            
        ];
    }
}

echo json_encode([
    'dashboard' => $response,
    'attendance' => $data
]);
?>
