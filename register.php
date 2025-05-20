 <?php
require_once 'config.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah digunakan
    $checkStmt = $pdo->prepare("SELECT * FROM User WHERE username = ? OR email = ?");
    $checkStmt->execute([$username, $email]);
    if ($checkStmt->rowCount() > 0) {
        $message = "Username or email already taken!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO User (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Registration failed. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - CiPrime</title>
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
        .register-container {
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
        input[type="text"], input[type="email"], input[type="password"] {
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
    <div class="register-container">
        <h2>Register on CiPrime</h2>
        <?php if ($message): ?>
            <div class="error"><?= $message ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <div class="back">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
