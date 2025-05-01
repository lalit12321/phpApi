<?php

$connect = mysqli_connect("localhost", "root", "", "student_manajment_system");

if (!$connect) {
    die(json_encode(array("status" => "error", "message" => "Database connection failed")));
}
?>