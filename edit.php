<?php
include 'includes/db.php';
$id = $_GET['id'];

$data = $conn->query("SELECT * FROM transactions WHERE id=$id")->fetch_assoc();
?>

<form method="POST">
    <input type="number" name="amount" value="<?= $data['amount'] ?>">
    <button name="update">Update</button>
</form>

<?php
if (isset($_POST['update'])) {
    $amount = $_POST['amount'];
    $conn->query("UPDATE transactions SET amount='$amount' WHERE id=$id");
    header("Location: transactions.php");
}
?>