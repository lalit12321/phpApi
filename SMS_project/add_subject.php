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
        if(isset($data['subject_id']) && isset($data['subject_name']) && isset($data['course_id'])){
            $subject_id = $data['subject_id'];
            $subject_name = $data['subject_name'];
            $course_id = $data['course_id'];
            
            
            // Insert into database
            $stmt = $connect->prepare("INSERT INTO subjects (subject_id, subject_name, course_id) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $subject_id, $subject_name, $course_id);
            
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
        $result = $connect->query("SELECT * FROM subjects");
        $departments = array();
        while($row = $result->fetch_assoc()){
            $departments[] = $row;
        }
        echo json_encode($departments);
        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['subject_id']) && isset($data['subject_name']) && isset($data['course_id'])){
            $subject_id = $data['subject_id'];
            $subject_name = $data['subject_name'];
            $course_id = $data['course_id'];
            
            // Update in database
            $stmt = $connect->prepare("UPDATE subjects SET subject_name = ?, course_id = ? WHERE subject_id = ?");
            $stmt->bind_param("ssi", $subject_name, $course_id, $subject_id);
            
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
        if(isset($data['subject_id'])){
            $subject_id = $data['subject_id'];
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM subjects WHERE subject_id = ?");
            $stmt->bind_param("i", $subject_id);
            
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