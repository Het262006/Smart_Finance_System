<?php
include 'includes/auth.php';
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';

$userId = $_SESSION['user_id'];


$where = "WHERE user_id = $userId";

if (!empty($_GET['type'])) {
    $type = $_GET['type'];
    $where .= " AND type='$type'";
}

if (!empty($_GET['from'])) {
    $where .= " AND trans_date >= '" . $_GET['from'] . "'";
}

if (!empty($_GET['to'])) {
    $where .= " AND trans_date <= '" . $_GET['to'] . "'";
}

$sql = "SELECT id, amount, type, category, description, trans_date 
        FROM transactions 
        $where
        ORDER BY trans_date DESC, id DESC";

$rows = $conn->query($sql);
?>

<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">Finance Tracker</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a class="active" href="transactions.php">Transactions</a>
            <a href="investment.php">Investment</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h2>Transactions</h2>
                <p>Add and manage your income and expenses.</p>
            </div>
        </div>


        <section class="panel form-panel">
            <h3>Add Transaction</h3>
            <form class="transaction-form" method="POST" action="save-transaction.php">
                <input type="number" step="0.01" min="0" name="amount" placeholder="Amount" required>

                <select id="type" name="type" required>
                    <option value="">Select type</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>

                <select id="category" name="category" required>
                    <option value="">Select category</option>
                </select>

                <input type="text" id="customCategory" name="custom_category"
                placeholder="Enter custom category" style="display:none;">

                <input type="text" name="description" placeholder="Description">
                <input type="date" name="trans_date" required>

                <button type="submit">Save Transaction</button>
            </form>
        </section>


        <section class="panel">
            <h3>Filter Transactions</h3>

<form method="GET" class="filter-form">
    
    <div class="filter-group">
        <label>From</label>
        <input type="date" name="from" value="<?= $_GET['from'] ?? '' ?>">
    </div>

    <div class="filter-group">
        <label>To</label>
        <input type="date" name="to" value="<?= $_GET['to'] ?? '' ?>">
    </div>

    <div class="filter-group">
        <label>Type</label>
        <select name="type">
            <option value="">All</option>
            <option value="income" <?= (($_GET['type'] ?? '') === 'income') ? 'selected' : '' ?>>Income</option>
            <option value="expense" <?= (($_GET['type'] ?? '') === 'expense') ? 'selected' : '' ?>>Expense</option>
        </select>
    </div>

    <div class="filter-group">
        <button type="submit">Apply Filter</button>
    </div>

</form>
        </section>


        <section class="panel">
            <div class="panel-head">
                <h3>All Transactions</h3>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($rows && $rows->num_rows > 0): ?>
                        <?php while ($row = $rows->fetch_assoc()): ?>
                            <tr>
                                <td class="cat-cell">
                                    <img src="<?php echo e(categoryIcon($row['category'])); ?>">
                                    <?php echo e($row['category']); ?>
                                </td>

                                <td><?php echo e(ucfirst($row['type'])); ?></td>

                                <td><?php echo e($row['description']); ?></td>

                                <td><?php echo e($row['trans_date']); ?></td>

                                <td class="<?php echo $row['type'] === 'income' ? 'income' : 'expense'; ?>">
                                    <?php echo $row['type'] === 'income' ? '+' : '-'; ?>
                                    ₹<?php echo number_format((float)$row['amount'], 2); ?>
                                </td>

                                <td>
                                    <a style="margin-right:20px; padding:6px 10px; background:#e0ecff; color:#2563eb; border-radius:6px;" 
                                    href="edit.php?id=<?php echo (int)$row['id']; ?>">Edit</a>

                                    <a style="padding:6px 10px; background:#ffe4e6; color:#dc2626; border-radius:6px;" 
                                    href="delete-transaction.php?id=<?php echo (int)$row['id']; ?>">
                                     Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </section>
    </main>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>