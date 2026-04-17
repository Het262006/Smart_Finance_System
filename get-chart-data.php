<?php
include 'includes/db.php';
session_start();

$userId = $_SESSION['user_id'];

$sql = "SELECT category, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = $userId AND type='expense'
        GROUP BY category";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>