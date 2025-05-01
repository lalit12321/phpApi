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
        if(isset($data['teacher_id']) && isset($data['name']) && isset($data['email']) && isset($data['department_id']) ){
            
            $teacher_id = $data['teacher_id'];
            $name = $data['name'];
            $email = $data['email'];
            $department_id = $data['department_id'];
            
            // Insert into database
            $stmt = $connect->prepare("INSERT INTO teachers (teacher_id, name, email, department_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $teacher_id, $name, $email, $department_id);
            
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
        $result = $connect->query("SELECT * FROM teachers");
        $departments = array();
        while($row = $result->fetch_assoc()){
            $departments[] = $row;
        }
        echo json_encode($departments);
        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['teacher_id']) && isset($data['name']) && isset($data['email']) && isset($data['department_id'])){
            $teacher_id = $data['teacher_id'];
            $name = $data['name'];
            $email = $data['email'];
            $department_id = $data['department_id'];
            
            // Update in database
            $stmt = $connect->prepare("UPDATE teachers SET name = ?, email = ?, department_id = ? WHERE teacher_id = ?");
            $stmt->bind_param("ssii", $name, $email, $department_id, $teacher_id);
            
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
        if(isset($data['teacher_id'])){
            $teacher_id = $data['teacher_id'];
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM teachers WHERE teacher_id = ?");
            $stmt->bind_param("i", $teacher_id);
            
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