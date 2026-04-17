<?php
include 'includes/auth.php';
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/header.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

$summaryStmt = $conn->prepare("
    SELECT
        COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE 0 END), 0) AS total_income,
        COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END), 0) AS total_expense
    FROM transactions
    WHERE user_id = ?
");
$summaryStmt->bind_param("i", $userId);
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc();

$totalIncome = (float)$summary['total_income'];
$totalExpense = (float)$summary['total_expense'];
$balance = $totalIncome - $totalExpense;

$stmt = $conn->prepare("SELECT SUM(amount) as total FROM investments WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$invested = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Current value
$stmt = $conn->prepare("SELECT SUM(current_value) as total FROM investments WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$currentValue = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Profit / Loss
$profit = $currentValue - $invested;

// Net Worth
$netWorth = $balance + $currentValue;

$listStmt = $conn->prepare("
    SELECT id, amount, type, category, description, trans_date
    FROM transactions
    WHERE user_id = ?
    ORDER BY trans_date DESC, id DESC
    LIMIT 8
");
$listStmt->bind_param("i", $userId);
$listStmt->execute();
$transactions = $listStmt->get_result();
?>

<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">Finance Tracker</div>
        <nav>
            <a class="active" href="dashboard.php">Dashboard</a>
            <a href="transactions.php">Transactions</a>
            <a href="investment.php">Investment</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h2>Hello, <?php echo e($userName); ?></h2>
                <p>Here’s your financial overview.</p>
            </div>
            <a href="transactions.php" class="btn-primary">+ Add Transaction</a>
        </div>

        <section class="card-grid">
            <div class="stat-card">
                <img src="assets/img/income.svg" alt="Income">
                <div>
                    <span>Total Income</span>
                    <h3>₹<?php echo number_format($totalIncome, 2); ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <img src="assets/img/expense.svg" alt="Expense">
                <div>
                    <span>Total Expense</span>
                    <h3>₹<?php echo number_format($totalExpense, 2); ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <img src="assets/img/balance.svg" alt="Balance">
                <div>
                    <span>Balance</span>
                    <h3>₹<?php echo number_format($balance, 2); ?></h3>
                </div>
            </div>

        </section>

        <section class="card-grid">

    <div class="stat-card">
        <span>Total Investment</span>
        <h3>₹<?php echo number_format($invested, 2); ?></h3>
    </div>

    <div class="stat-card">
        <span>Current Value</span>
        <h3>₹<?php echo number_format($currentValue, 2); ?></h3>
    </div>

    <div class="stat-card">
        <span>Profit / Loss</span>
        <h3 class="<?php echo $profit >= 0 ? 'income' : 'expense'; ?>">
            <?php echo ($profit >= 0 ? '+' : '-') . '₹' . number_format(abs($profit), 2); ?>
        </h3>
    </div>

    <div class="stat-card">
        <span>Net Worth</span>
        <h3>₹<?php echo number_format($netWorth, 2); ?></h3>
    </div>

</section>

    <section class="panel chart-panel">
        <h3>Expense Breakdown</h3>
        <div class="chart-box">
            <canvas id="expenseChart"></canvas>
        </div>
    </section>

<section class="panel">
    <h3>Income vs Expense</h3>

    <div class="chart-box">
        <canvas id="incomeExpenseChart"></canvas>
    </div>

</section>

        <section class="panel">
            <div class="panel-head">
                <h3>Recent Transactions</h3>
                <a href="transactions.php">View all</a>
            </div>

    <section class="panel chart-panel">
        <h3>Investment Distribution</h3>
        <div class="chart-box">
            <canvas id="investmentChart"></canvas>
        </div>
    </section>

            <?php if ($transactions->num_rows > 0): ?>
                <div class="transaction-list">
                    <?php while ($row = $transactions->fetch_assoc()): ?>
                        <div class="transaction-item">
                            <div class="transaction-left">
                                <img src="<?php echo e(categoryIcon($row['category'])); ?>" alt="<?php echo e($row['category']); ?>">
                                <div>
                                    <strong><?php echo e($row['category']); ?></strong>
                                    <small><?php echo e($row['description'] ?: 'No description'); ?></small>
                                </div>
                            </div>
                            <div class="transaction-right">
                                <span class="<?php echo $row['type'] === 'income' ? 'amount income' : 'amount expense'; ?>">
                                    <?php echo $row['type'] === 'income' ? '+' : '-'; ?>₹<?php echo number_format((float)$row['amount'], 2); ?>
                                </span>
                                <small><?php echo e($row['trans_date']); ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <img src="assets/img/empty-state.svg" alt="No transactions">
                    <p>No transactions yet. Add your first one.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
    </div> <!-- row -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>