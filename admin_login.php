 <?php
session_start();
require_once 'config.php';

$error = '';

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM Admin WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && $admin['password'] === $password) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['user_role'] = 'admin';
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            background: #0f2027;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .login-box {
            background: #1e293b;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #3b82f6;
            width: 300px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
        }
        input[type="submit"] {
            background: #3b82f6;
            color: white;
            cursor: pointer;
        }
        .error {
            color: #f43f5e;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
