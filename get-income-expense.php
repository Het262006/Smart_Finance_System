<?php
include 'includes/db.php';
session_start();

$userId = $_SESSION['user_id'];

$sql = "SELECT 
SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
FROM transactions WHERE user_id=$userId";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode($data);
?>