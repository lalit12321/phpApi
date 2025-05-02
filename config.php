<?php

$connect = mysqli_connect("sql111.infinityfree.com", "if0_38796249", "Lalit5400", "if0_38796249_ragistration");

if (!$connect) {
    die(json_encode(array("status" => "error", "message" => "Database connection failed")));
}
?>