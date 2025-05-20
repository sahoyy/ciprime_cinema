 <?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Cek apakah sudah login dan role-nya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Ambil data statistik pakai PDO
try {
    $user_count = $pdo->query("SELECT COUNT(*) AS total FROM User")->fetch()['total'];
    $movie_count = $pdo->query("SELECT COUNT(*) AS total FROM Movie")->fetch()['total'];
    $schedule_count = $pdo->query("SELECT COUNT(*) AS total FROM Schedule")->fetch()['total'];
    $payment_count = $pdo->query("SELECT COUNT(*) AS total FROM Payment")->fetch()['total'];
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Ciprime</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #0f172a, #1e293b);
            color: white;
        }

        header {
            background-color: #111827;
            padding: 20px;
            text-align: center;
            color: #f43f5e;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 1px;
            border-bottom: 4px solid #3b82f6;
        }

        nav {
            background-color: #1f2937;
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            background-color: #374151;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #3b82f6;
        }

        .dashboard {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            padding: 40px 20px;
        }

        .card {
            background-color: #1e40af;
            border-radius: 15px;
            padding: 30px;
            width: 220px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h2 {
            margin: 0;
            font-size: 48px;
            color: #facc15;
        }

        .card p {
            margin-top: 10px;
            font-size: 18px;
            color: #e0e7ff;
        }
    </style>
</head>
<body>

<header>
    Ciprime Admin Dashboard
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="now_showing.php">Now Showing</a>
    <a href="seat_selection.php">Seat Selection</a>
    <a href="checkout.php">Payment</a>
    <a href="my_tickets.php">Ticketing Info</a>
    <a href="logout.php" style="background-color: #ef4444;">Logout</a>
</nav>

<div class="dashboard">
    <div class="card">
        <h2><?= $user_count ?></h2>
        <p>Users</p>
    </div>
    <div class="card">
        <h2><?= $movie_count ?></h2>
        <p>Movies</p>
    </div>
    <div class="card">
        <h2><?= $schedule_count ?></h2>
        <p>Schedules</p>
    </div>
    <div class="card">
        <h2><?= $payment_count ?></h2>
        <p>Transactions</p>
    </div>
</div>

</body>
</html>
