<?php
include 'includes/auth.php';
include 'includes/db.php';
include 'includes/header.php';

$userId = $_SESSION['user_id'];

// Fetch investments
$stmt = $conn->prepare("SELECT * FROM investments WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Summary calculations
$totalInvested = 0;
$totalCurrent = 0;
?>

<div class="app-shell">

    <aside class="sidebar">
        <div class="brand">Finance Tracker</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="transactions.php">Transactions</a>
            <a class="active" href="investment.php">Investment</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <h2>Investment</h2>
            <p>Track your investments and growth.</p>
        </div>

        <!-- ADD INVESTMENT -->
        <section class="panel">
            <h3>Add Investment</h3>

            <form method="POST" action="save-investment.php" class="transaction-form">
                <input type="text" name="name" placeholder="Investment Name (Stock, FD, Crypto)" required>
                <input type="number" step="0.01" name="amount" placeholder="Amount Invested" required>
                <input type="number" step="0.01" name="current_value" placeholder="Current Value" required>
                <input type="date" name="date" required>
                <button type="submit">Add Investment</button>
            </form>
        </section>

        <!-- SUMMARY CARDS -->
        <section class="card-grid">
            <?php
            $tempResult = $result;
            while ($row = $tempResult->fetch_assoc()) {
                $totalInvested += $row['amount'];
                $totalCurrent += $row['current_value'];
            }

            $totalProfit = $totalCurrent - $totalInvested;
            ?>

            <div class="stat-card">
                <span>Total Invested</span>
                <h3>₹<?php echo number_format($totalInvested, 2); ?></h3>
            </div>

            <div class="stat-card">
                <span>Current Value</span>
                <h3>₹<?php echo number_format($totalCurrent, 2); ?></h3>
            </div>

            <div class="stat-card">
                <span>Total Profit/Loss</span>
                <h3 class="<?php echo $totalProfit >= 0 ? 'income' : 'expense'; ?>">
                    <?php echo ($totalProfit >= 0 ? '+' : '-') . '₹' . number_format(abs($totalProfit), 2); ?>
                </h3>
            </div>
        </section>

        <!-- RESET RESULT POINTER -->
        <?php
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <!-- LIST -->
        <section class="panel">
            <h3>Your Investments</h3>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Invested</th>
                        <th>Current</th>
                        <th>Profit/Loss</th>
                        <th>ROI %</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $profit = $row['current_value'] - $row['amount'];
                            $roi = ($row['amount'] > 0) 
                                ? (($profit / $row['amount']) * 100) 
                                : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td>₹<?php echo number_format($row['current_value'], 2); ?></td>

                            <td class="<?php echo $profit >= 0 ? 'income' : 'expense'; ?>">
                                <?php echo ($profit >= 0 ? '+' : '-') . '₹' . number_format(abs($profit), 2); ?>
                            </td>

                            <td class="<?php echo $roi >= 0 ? 'income' : 'expense'; ?>">
                                <?php echo number_format($roi, 2); ?>%
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No investments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>