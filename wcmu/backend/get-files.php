<?php
header('Content-Type: application/json');

$host = "localhost";
$db   = "simple_upload_db";
$user = "root";
$pass = "";


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit;
}

$sql    = "SELECT id, file_name, monthUploaded, yearUploaded, file_path, uploaded_at FROM files ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

$files = [];

while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

echo json_encode(["success" => true, "data" => $files]);