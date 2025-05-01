<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../config.php');

$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

if (isset($data['department_id'], $data['department_name'], $data['department_code'])) {
    $department_id = $data['department_id'];
    $department_name = $data['department_name'];
    $department_code = $data['department_code'];

    // Validate input
    if (empty($department_id) || empty($department_name) || empty($department_code)) {
        http_response_code(400);
        echo json_encode(array("message" => "All fields are required."));
        exit;
    }

    $check_id = $connect->prepare("SELECT department_id FROM department WHERE department_id = ?");
    $check_id->bind_param("i", $department_id);
    $check_id->execute();
    $check_id->store_result();
    if ($check_id->num_rows > 0) {
        http_response_code(409);
        echo json_encode(array("message" => "Department ID already exists."));
        $check_id->close();
        exit;
    }
    $check_id->close();
    
    // Check for duplicate department_name
    $check_name = $connect->prepare("SELECT department_name FROM department WHERE department_name = ?");
    $check_name->bind_param("s", $department_name);
    $check_name->execute();
    $check_name->store_result();
    if ($check_name->num_rows > 0) {
        http_response_code(409);
        echo json_encode(array("message" => "Department Name already exists."));
        $check_name->close();
        exit;
    }
    $check_name->close();
    
    // Check for duplicate department_code
    $check_code = $connect->prepare("SELECT department_code FROM department WHERE department_code = ?");
    $check_code->bind_param("s", $department_code);
    $check_code->execute();
    $check_code->store_result();
    if ($check_code->num_rows > 0) {
        http_response_code(409);
        echo json_encode(array("message" => "Department Code already exists."));
        $check_code->close();
        exit;
    }
    $check_code->close();

    $stmt = $connect->prepare("INSERT INTO department (department_id, department_name, department_code) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $department_id, $department_name, $department_code);

    if ($stmt->execute()) {
        http_response_code(201); 
        echo json_encode(array("message" => "Department added successfully."));
    } else {
        http_response_code(500); 
        echo json_encode(array("message" => "Failed to add department: " . $stmt->error));
    }

    $stmt->close();
} else {
    http_response_code(400); 
    echo json_encode(array("message" => "Missing required fields."));
}
break;

    case 'GET':
        // Get all departments
        $stmt = $connect->prepare("SELECT * FROM department");
        $stmt->execute();
        $result = $stmt->get_result();
        $departments = array();
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        $stmt->close();
        if (count($departments) > 0) {
            http_response_code(200); 
            echo json_encode($departments);
        } else {
            http_response_code(404); 
            echo json_encode(array("message" => "No departments found."));
        }

        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['department_id'], $data['department_name'], $data['department_code'])) {
            $department_id = $data['department_id'];
            $department_name = $data['department_name'];
            $department_code = $data['department_code'];

            // Validate input
            if (empty($department_id) || empty($department_name) || empty($department_code)) {
                http_response_code(400);
                echo json_encode(array("message" => "All fields are required."));
                exit;
            }

            $stmt = $connect->prepare("UPDATE department SET department_name = ?, department_code = ? WHERE department_id = ?");
            $stmt->bind_param("ssi", $department_name, $department_code, $department_id);

            if ($stmt->execute()) {
                http_response_code(200); 
                echo json_encode(array("message" => "Department updated successfully."));
            } else {
                http_response_code(500); 
                echo json_encode(array("message" => "Failed to update department: " . $stmt->error));
            }

            $stmt->close();
        } else {
            http_response_code(400); 
            echo json_encode(array("message" => "Missing required fields."));
        }

        break;
    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        exit;

}

?>