<?php
include 'includes/auth.php';
include 'includes/db.php';

$userId = $_SESSION['user_id'];

$name = $_POST['name'];
$amount = $_POST['amount'];
$current = $_POST['current_value'];
$date = $_POST['date'];

$stmt = $conn->prepare("
    INSERT INTO investments (user_id, name, amount, current_value, date)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("isdds", $userId, $name, $amount, $current, $date);
$stmt->execute();

header("Location: investment.php");
exit;