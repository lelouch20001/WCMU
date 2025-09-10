<?php
header('Content-Type: application/json');

$host = "localhost";
$db   = "simple_upload_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fileName      = $_POST["fileName"] ?? null;
    $monthUploaded = $_POST["monthUploaded"] ?? null;
    $yearUploaded = $_POST["yearUploaded"] ?? null;
    $file          = $_FILES["pdfFile"] ?? null;

    if (! $fileName || ! $monthUploaded || ! $yearUploaded || ! $file) {
        echo json_encode(["success" => false, "message" => "Missing fields"]);
        exit;
    }

    if ($file["type"] !== "application/pdf") {
        echo json_encode(["success" => false, "message" => "Only PDF allowed"]);
        exit;
    }

    $uploadDir = "../uploads/";
    if (! is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newFileName = uniqid() . "-" . basename($file["name"]);
    $targetPath  = $uploadDir . $newFileName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO files (file_name, monthUploaded, yearUploaded, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fileName, $monthUploaded, $yearUploaded, $newFileName);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Upload failed"]);
    }
}
