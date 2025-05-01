<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['course_id'], $data['course_name'], $data['department_id'])) {
            echo json_encode(array("status" => "error", "message" => "Missing required fields"));
            exit;
        }
        $stmt = $connect->prepare("INSERT INTO courses (course_id, course_name, department_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $data['course_od'], $data['course_name'], $data['department_id']);
        
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Data inserted successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to insert data: " . $stmt->error));
        }
        
        $stmt->close();
        break;
    case 'GET':
            if (isset($_GET['id'])) {
                // Get course by ID
                $id = $_GET['id'];
                $stmt = $connect->prepare("SELECT * FROM courses WHERE course_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                echo json_encode($data);
                $stmt->close();
        
            } elseif (isset($_GET['search'])) {
                // Search course by name (partial match)
                $search = "%" . $_GET['search'] . "%";
                $stmt = $connect->prepare("SELECT * FROM courses WHERE course_name LIKE ?");
                $stmt->bind_param("s", $search);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($data);
                $stmt->close();
        
            } else {
                // Get all courses
                $stmt = $connect->prepare("SELECT * FROM courses");
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($data);
                $stmt->close();
            }
            break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['course_id'], $data['course_name'], $data['department_id'])) {
            echo json_encode(array("status" => "error", "message" => "Missing required fields"));
            exit;
        }
        $stmt = $connect->prepare("UPDATE courses SET course_name=?, department_id=? WHERE course_id=?");
        $stmt->bind_param("sii", $data['course_name'], $data['department_id'], $data['course_id']);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(array("status" => "success", "message" => "Data updated successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to update data: " . $stmt->error));
        }
        $stmt->close();
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['course_id'])) {
            echo json_encode(array("status" => "error", "message" => "Missing required fields"));
            exit;
        }
        
        $stmt = $connect->prepare("DELETE FROM courses WHERE course_id=?");
        $stmt->bind_param("i", $data['course_id']);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(array("status" => "success", "message" => "Data deleted successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "No record found with the given course_id"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to delete data: " . $stmt->error));
        }
        
        $stmt->close();
        break;        
    default:
        echo json_encode(array("status" => "error", "message" => "Invalid request method"));
        break;
    }
?>