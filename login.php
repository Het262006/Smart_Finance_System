<?php
include 'includes/db.php';
include 'includes/header.php';

// ✅ FIX SESSION WARNING
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="auth-page">

    <!-- LEFT IMAGE -->
    <div class="auth-visual">
        <img src="assets/img/login-illustration.svg" alt="Finance illustration">
    </div>

    <!-- RIGHT FORM -->
    <div class="auth-card">

   
        <h1 class="logo">Smart Finance Tracker</h1>

        <h2>Welcome back</h2>
        <p>Track your income, expenses and manage your finances smarter.</p>

        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="form">

            <input type="email" 
                   name="email" 
                   placeholder="Email" 
                   value="<?php echo htmlspecialchars($email); ?>" 
                   required>

            <input type="password" 
                   id="password"
                   name="password" 
                   placeholder="Password" 
                   required>

            <button type="submit">Login</button>

        </form>

        <div class="auth-link">
            Don’t have an account? <a href="register.php">Register</a>
        </div>

    </div>
</div>

</body>
</html>