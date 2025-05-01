<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../config.php');

$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'POST':
        // Add a new department
        $data = json_decode(file_get_contents("php://input"), true);

if (isset($data['attendance_id']) && isset($data['student_id']) && isset($data['subject_id']) && isset($data['status'])) {
    $attendance_id = $data['attendance_id'];
    $student_id = $data['student_id'];
    $subject_id = $data['subject_id'];
    $status = $data['status'];
    
    // Automatically get today's date
    $date = date("Y-m-d");

    // Insert into database
    $stmt = $connect->prepare("INSERT INTO attendances (attendance_id, student_id, subject_id, date, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $attendance_id, $student_id, $subject_id, $date, $status);

    if ($stmt->execute()) {
        echo json_encode(array("message" => "Attendance added successfully"));
    } else {
        echo json_encode(array("message" => "Failed to add attendance"));
    }
    $stmt->close();
} else {
    echo json_encode(array("message" => "Invalid input"));
}

        break;
        case 'GET':
            // Get attendance with student name and class
            $stmt = $connect->prepare("
                SELECT a.*, s.name, s.class
                FROM attendances a
                JOIN students s ON a.student_id = s.student_id
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            $attendances = array();
            while ($row = $result->fetch_assoc()) {
                $attendances[] = $row;
            }
            echo json_encode($attendances);
            break;
        
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['attendance_id']) && isset($data['student_id']) && isset($data['subject_id']) && isset($data['status'])) {
            $attendance_id = $data['attendance_id'];
            $student_id = $data['student_id'];
            $subject_id = $data['subject_id'];
            $status = $data['status'];
            
            // Update in database
            $stmt = $connect->prepare("UPDATE attendances SET student_id=?, subject_id=?, status=? WHERE attendance_id=?");
            $stmt->bind_param("iisi", $student_id, $subject_id, $status, $attendance_id);

            if ($stmt->execute()) {
                echo json_encode(array("message" => "Attendance updated successfully"));
            } else {
                echo json_encode(array("message" => "Failed to update attendance"));
            }
            $stmt->close();
        } else {
            echo json_encode(array("message" => "Invalid input"));
        }

        break;
    case 'DELETE':
        // Delete a department
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['attendance_id'])) {
            $attendance_id = $data['attendance_id'];
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM attendances WHERE attendance_id=?");
            $stmt->bind_param("i", $attendance_id);

            if ($stmt->execute()) {
                echo json_encode(array("message" => "Attendance deleted successfully"));
            } else {
                echo json_encode(array("message" => "Failed to delete attendance"));
            }
            $stmt->close();
        } else {
            echo json_encode(array("message" => "Invalid input"));
        }
        break;
    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        exit;

}

?>