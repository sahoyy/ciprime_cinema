 <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_POST['schedule_id'], $_POST['selected_seats'])) {
    die("Invalid access.");
}

$user_id = $_SESSION['user_id'];
$schedule_id = $_POST['schedule_id'];
$seat_ids = explode(',', $_POST['selected_seats']);

$stmt = $pdo->prepare("SELECT s.*, m.title, st.studio_name FROM Schedule s JOIN Movie m ON s.movie_id = m.movie_id JOIN Studio st ON s.studio_id = st.studio_id WHERE s.schedule_id = ?");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    die("Schedule not found.");
}

$inPlaceholders = implode(',', array_fill(0, count($seat_ids), '?'));
$seatStmt = $pdo->prepare("SELECT seat_number FROM Seat WHERE seat_id IN ($inPlaceholders)");
$seatStmt->execute($seat_ids);
$seatNumbers = $seatStmt->fetchAll(PDO::FETCH_COLUMN);

$total_price = $schedule['price'] * count($seat_ids);
?>
<?php include 'header.php'; ?>
<div class="container">
    <h2>Checkout</h2>
    <p><strong>Movie:</strong> <?= htmlspecialchars($schedule['title']) ?></p>
    <p><strong>Date:</strong> <?= $schedule['date'] ?> | <strong>Time:</strong> <?= $schedule['time'] ?></p>
    <p><strong>Studio:</strong> <?= $schedule['studio_name'] ?></p>
    <p><strong>Seats:</strong> <?= implode(', ', $seatNumbers) ?></p>
    <p><strong>Total Price:</strong> Rp<?= number_format($total_price, 0, ',', '.') ?></p>
    
    <form action="submit_payment.php" method="post">
        <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">
        <input type="hidden" name="selected_seats" value="<?= htmlspecialchars($_POST['selected_seats']) ?>">
        <input type="hidden" name="total_price" value="<?= $total_price ?>">
        
        <label for="payment_method">Payment Method:</label><br>
        <select name="payment_method" id="payment_method" required style="padding: 10px; border-radius: 6px; width: 100%; margin-top: 10px;">
            <option value="">-- Select Payment --</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="E-Wallet">E-Wallet</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
        
        <button type="submit" class="submit-btn" style="margin-top:20px;">Confirm & Pay</button>
    </form>
</div>

<style>
.container {
    background: #1a1a1a;
    padding: 25px;
    border-radius: 15px;
    max-width: 700px;
    margin: auto;
    color: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.6);
}

h2 {
    color: #ff4d4d;
}

.submit-btn {
    padding: 10px 20px;
    background-color: #ff4d4d;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
}

.submit-btn:hover {
    background-color: #ff3333;
}
</style>
<?php include 'footer.php'; ?>