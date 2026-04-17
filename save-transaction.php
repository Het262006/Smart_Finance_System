<?php
include 'includes/auth.php';
include 'includes/db.php';

$userId = $_SESSION['user_id'];

// Get data
$amount = (float)($_POST['amount'] ?? 0);
$type = trim($_POST['type'] ?? '');
$category = trim($_POST['category'] ?? '');

// Handle custom category
if ($category === "Other" && !empty($_POST['custom_category'])) {
    $category = trim($_POST['custom_category']);
}

$description = trim($_POST['description'] ?? '');
$transDate = $_POST['trans_date'] ?? '';

// Allowed types only
$allowedTypes = ['income', 'expense'];

// ✅ VALIDATION (UPDATED)
if (
    $amount <= 0 ||
    !in_array($type, $allowedTypes, true) ||
    $category === '' ||   // only check empty, not strict list
    $transDate === ''
) {
    header("Location: transactions.php");
    exit;
}

// Insert into database
$stmt = $conn->prepare("
    INSERT INTO transactions (user_id, amount, type, category, description, trans_date)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("idssss", $userId, $amount, $type, $category, $description, $transDate);
$stmt->execute();

header("Location: transactions.php");
exit;
?>