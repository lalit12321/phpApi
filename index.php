<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);
echo json_encode($data);

// Check for missing required fields


 $connect = mysqli_connect("localhost", "root", "", "students");

if (!$connect) {
    die(json_encode(array("status" => "error", "message" => "Database connection failed")));
}

// // Prepare and bind
$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'GET':
        $stmt = $connect->prepare("SELECT * FROM sturgi");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
        break;
    case 'POST':
        if (!isset($data['name'], $data['email'], $data['password'], $data['usrname'])) {
            echo json_encode(array("status" => "error", "message" => "Missing required fields"));
            exit;
        }
        $stmt = $connect->prepare("INSERT INTO sturgi (name, email, password, usrname) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data['name'], $data['email'], $data['password'], $data['usrname']);
        
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Data inserted successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to insert data: " . $stmt->error));
        }
        
        $stmt->close();
        break;
    case 'PUT':
        if (!isset($data['id'], $data['name'], $data['email'], $data['password'], $data['usrname'])) {
            echo json_encode(array("status" => "error", "message" => "Missing required fields"));
            exit;
        }
        $stmt = $connect->prepare("UPDATE sturgi SET name=?, email=?, password=?, usrname=? WHERE id=?");
        $stmt->bind_param("ssssi", $data['name'], $data['email'], $data['password'], $data['usrname'], $data['id']);
        
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Data updated successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to update data: " . $stmt->error));
        }
        
        $stmt->close();
        break;
    case 'DELETE':
        if (!isset($data['id'])) {
            echo json_encode(array("status" => "error", "message" => "Missing required fields"));
            exit;
        }
        $stmt = $connect->prepare("DELETE FROM sturgi WHERE id=?");
        $stmt->bind_param("i", $data['id']);
        
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Data deleted successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to delete data: " . $stmt->error));
        }
        
        $stmt->close();
        break;
}

$connect->close();
?>
