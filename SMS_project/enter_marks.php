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
        if(isset($data['mark_id']) && isset($data['student_id']) && isset($data['subject_id']) && isset($data['exam_type']) && isset($data['marks_obtained']) && isset($data['max_marks'])){
            $mark_id = $data['mark_id'];
            $student_id = $data['student_id'];
            $subject_id = $data['subject_id'];
            $exam_type = $data['exam_type'];
            $marks_obtained = $data['marks_obtained'];
            $max_marks = $data['max_marks'];
            
            // Insert into database
            $stmt = $connect->prepare("INSERT INTO marks (mark_id,student_id,subject_id,exam_type,marks_obtained,max_marks) VALUES (?, ?,?,?,?,?)");
            $stmt->bind_param("iiisii", $mark_id, $student_id, $subject_id, $exam_type, $marks_obtained, $max_marks);
            
            if($stmt->execute()){
                echo json_encode(array("message" => "Department added successfully"));
            } else {
                echo json_encode(array("message" => "Failed to add department"));
            }
            $stmt->close();
        } else {
            echo json_encode(array("message" => "Invalid input"));
        }
        break;
    case 'GET':
        // get all departments
        $stmt = $connect->prepare("SELECT * FROM marks");
        $stmt->execute();
        $result = $stmt->get_result();
        $departments = array();
        while($row = $result->fetch_assoc()){
            $departments[] = $row;
        }
        echo json_encode($departments);
        $stmt->close();
        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['mark_id']) && isset($data['student_id']) && isset($data['subject_id']) && isset($data['exam_type']) && isset($data['marks_obtained']) && isset($data['max_marks'])){
            $mark_id = $data['mark_id'];
            $student_id = $data['student_id'];
            $subject_id = $data['subject_id'];
            $exam_type = $data['exam_type'];
            $marks_obtained = $data['marks_obtained'];
            $max_marks = $data['max_marks'];
            
            // Update in database
            $stmt = $connect->prepare("UPDATE marks SET student_id=?, subject_id=?, exam_type=?, marks_obtained=?, max_marks=? WHERE mark_id=?");
            $stmt->bind_param("iiisii", $student_id, $subject_id, $exam_type, $marks_obtained, $max_marks, $mark_id);
            
            if($stmt->execute()){
                echo json_encode(array("message" => "Department updated successfully"));
            } else {
                echo json_encode(array("message" => "Failed to update department"));
            }
            $stmt->close();
        } else {
            echo json_encode(array("message" => "Invalid input"));
        }
        break;
    case 'DELETE':
        // Delete a department
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['mark_id'])){
            $mark_id = $data['mark_id'];
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM marks WHERE mark_id=?");
            $stmt->bind_param("i", $mark_id);
            
            if($stmt->execute()){
                echo json_encode(array("message" => "Department deleted successfully"));
            } else {
                echo json_encode(array("message" => "Failed to delete department"));
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