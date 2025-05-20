 <?php
session_start();
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM User WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["user_id"] = $user['user_id'];
        $_SESSION["username"] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $message = "Incorrect username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CiPrime</title>
    <style>
        body {
            background-color: #0d0d0d;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #1a1a1a;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(229, 9, 20, 0.6);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #e50914;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 8px;
            background: #2e2e2e;
            color: #fff;
        }
        button {
            width: 100%;
            background: #e50914;
            color: #fff;
            border: none;
            padding: 12px;
            margin-top: 15px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #ff1f1f;
        }
        .error {
            color: #ff4d4d;
            text-align: center;
            margin-bottom: 10px;
        }
        .back {
            text-align: center;
            margin-top: 15px;
        }
        .back a {
            color: #e50914;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to CiPrime</h2>
        <?php if ($message): ?>
            <div class="error"><?= $message ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="back">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
