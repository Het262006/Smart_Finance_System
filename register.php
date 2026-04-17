<?php
include 'includes/db.php';
include 'includes/header.php';

$name = '';
$email = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hash);

            if ($stmt->execute()) {
                $success = "Registration successful. You can login now.";
                $name = '';
                $email = '';
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-visual">
        <img src="assets/img/login-illustration.svg" alt="Finance illustration">
    </div>

    <div class="auth-card">
        <h1>Create account</h1>
        <p>Track income, expenses, and your monthly balance.</p>

        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="form">
            <input type="text" name="name" placeholder="Full name" value="<?php echo htmlspecialchars($name); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <div class="auth-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</div>

</body>
</html>