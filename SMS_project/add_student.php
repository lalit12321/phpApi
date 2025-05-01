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
        if(isset($data['student_id']) && isset($data['name']) && isset($data['email']) && isset($data['phone']) && isset($data['course_id']) && isset($data['gender']) && isset($data['roll_no'])){
            $student_id = $data['student_id'];
            $name = $data['name'];
            $email = $data['email'];
            $phone = $data['phone'];
            $course_id = $data['course_id'];
            $gender = $data['gender'];
            $roll_no = $data['roll_no'];
            $stmt = $connect->prepare("INSERT INTO students (student_id, name, email, phone, course_id, gender, roll_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississi", $student_id, $name, $email, $phone, $course_id, $gender, $roll_no);
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
        $result = $connect->query("SELECT * FROM students");
        $departments = array();
        while($row = $result->fetch_assoc()){
            $departments[] = $row;
        }
        echo json_encode($departments);
        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['student_id']) && isset($data['name']) && isset($data['email']) && isset($data['phone']) && isset($data['course_id']) && isset($data['gender']) && isset($data['roll_no'])){
            $student_id = $data['student_id'];
            $name = $data['name'];
            $email = $data['email'];
            $phone = $data['phone'];
            $course_id = $data['course_id'];
            $gender = $data['gender'];
            $roll_no = $data['roll_no'];
            $stmt = $connect->prepare("UPDATE students SET name = ?, email = ?, phone = ?, course_id=?, gender=?, roll_no=? WHERE student_id = ?");
            $stmt->bind_param("ssissii", $name, $email, $phone, $course_id, $gender, $roll_no, $student_id);
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
        if(isset($data['student_id'])){
            $student_id = $data['student_id'];
            $stmt = $connect->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->bind_param("i", $student_id);
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