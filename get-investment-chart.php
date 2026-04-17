<?php
include 'includes/db.php';
session_start();

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT name, current_value 
    FROM investments 
    WHERE user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);