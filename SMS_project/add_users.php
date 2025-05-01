<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../config.php');

$method = $_SERVER['REQUEST_METHOD'];
switch($method){

        case 'POST':
            // Get POST request data
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Check if required fields are provided
            if (!isset($data['user_id'], $data['username'], $data['password'], $data['role'], $data['student_id'])) {
                echo json_encode(array("status" => "error", "message" => "Missing required fields"));
                exit;
            }
            
            // Validate input
            $user_id = $data['user_id'];
            $username = $data['username'];
            $password = $data['password'];
            $role = $data['role'];
            $student_id = $data['student_id'];
            // Validate empty fields
            if(empty($user_id) || empty($username) || empty($password) || empty($role) || empty($student_id)) {
                echo json_encode(array("status" => "error", "message" => "Fields cannot be empty"));
                exit;
            }
    
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
            // Prepare SQL query for user registration
            $stmt = $connect->prepare("INSERT INTO users (user_id, username, password, role, student_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $username, $hashed_password, $role, $student_id);
            
            // Execute query
            if ($stmt->execute()) {
                echo json_encode(array("status" => "success", "message" => "User registered successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Failed to register user"));
            }
    
            $stmt->close();
            break;
        
    case 'GET':
        // get all departments
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];  // user_id ko URL se fetch kar rahe hain
            
            // Prepare query to fetch user data
            $stmt = $connect->prepare("SELECT user_id, username, role, student_id FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id); // user_id ko parameter ke roop mein bind kar rahe hain
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Agar user milta hai, to data ko fetch kar ke JSON response bhejte hain
                $user = $result->fetch_assoc();
                echo json_encode(["status" => "success", "user" => $user]);
            } else {
                // Agar user nahi milta, to error response
                echo json_encode(["status" => "error", "message" => "User not found"]);
            }
            
            $stmt->close();
        } else {
            // Agar user_id pass nahi kiya gaya, to error response
            echo json_encode(["status" => "error", "message" => "user_id is required"]);
        }
        break;
    case 'PUT':
        // Update a department
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['user_id'], $data['username'], $data['role'])) {
            $user_id = $data['user_id'];
            $username = $data['username'];
            $role = $data['role'];
            
            // Update in database
            $stmt = $connect->prepare("UPDATE users SET username = ?, role = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $username, $role, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(array("message" => "User updated successfully"));
            } else {
                echo json_encode(array("message" => "Failed to update user"));
            }
            $stmt->close();
        } else {
            echo json_encode(array("message" => "Invalid input"));
        }

        break;
    case 'DELETE':
        // Delete a department
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(array("message" => "User deleted successfully"));
            } else {
                echo json_encode(array("message" => "Failed to delete user"));
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