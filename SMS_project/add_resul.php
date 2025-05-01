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
        if(isset($data['result_id']) && isset($data['student_id']) && isset($data['semester']) && isset($data['total_marks']) && isset($data['percentage']) && isset($data['grade'])){
            $result_id = $data['result_id'];
            $student_id = $data['student_id'];
            $semester = $data['semester'];
            $total_marks = $data['total_marks'];
            $percentage = $data['percentage'];
            $grade = $data['grade'];
            
            
            // Insert into database
            $stmt = $connect->prepare("INSERT INTO results (result_id,student_id,semester,total_marks,percentage,grade) VALUES (?,?,?,?, ?,?)");
            $stmt->bind_param("iiiiis", $result_id, $student_id, $semester, $total_marks, $percentage, $grade);
            
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
        // Get all departments
        $result = $connect->query("SELECT * FROM results");
        $departments = array();
        while($row = $result->fetch_assoc()){
            $departments[] = $row;
        }
        echo json_encode($departments);
        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['result_id']) && isset($data['student_id']) && isset($data['semester']) && isset($data['total_marks']) && isset($data['percentage']) && isset($data['grade'])){
            $result_id = $data['result_id'];
            $student_id = $data['student_id'];
            $semester = $data['semester'];
            $total_marks = $data['total_marks'];
            $percentage = $data['percentage'];
            $grade = $data['grade'];
            
            // Update in database
            $stmt = $connect->prepare("UPDATE results SET student_id = ?, semester = ?, total_marks = ?, percentage = ?, grade = ? WHERE result_id = ?");
            $stmt->bind_param("iisisi", $student_id, $semester, $total_marks, $percentage, $grade, $result_id);
            
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
        if(isset($data['result_id'])){
            $result_id = $data['result_id'];
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM results WHERE result_id = ?");
            $stmt->bind_param("i", $result_id);
            
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