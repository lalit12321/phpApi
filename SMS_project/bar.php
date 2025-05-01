<?php
// dashboard.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../config.php');
// Database connection (make sure to adjust credentials)
// Query to get the count of students present on each weekday
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
            'day' => $row['day'],   // Day of the week (e.g., 'Monday')
            'students' => $row['student_count'], // Number of students present that day
        ];
    }
}

echo json_encode($data);
?>

?>