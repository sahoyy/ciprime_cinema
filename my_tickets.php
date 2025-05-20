<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data semua tiket milik user
$stmt = $pdo->prepare("
    SELECT 
        b.booking_id, b.booking_date, b.total_price,
        s.date AS show_date, s.time, 
        m.title AS movie_title,
        st.studio_name,
        GROUP_CONCAT(se.seat_number ORDER BY se.seat_number) AS seats,
        p.payment_method, p.status AS payment_status
    FROM Booking b
    JOIN Schedule s ON b.schedule_id = s.schedule_id
    JOIN Movie m ON s.movie_id = m.movie_id
    JOIN Studio st ON s.studio_id = st.studio_id
    JOIN Booking_Detail bd ON b.booking_id = bd.booking_id
    JOIN Seat se ON bd.seat_id = se.seat_id
    LEFT JOIN Payment p ON p.booking_id = b.booking_id
    WHERE b.user_id = ?
    GROUP BY b.booking_id
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Your Tickets</h2>
    <?php if (count($tickets) === 0): ?>
        <p>You have no tickets yet.</p>
    <?php else: ?>
        <div class="ticket-list">
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-card">
                    <div class="ticket-header">
                        🎟️ <?= htmlspecialchars($ticket['movie_title']) ?>
                    </div>
                    <div class="ticket-body">
                        <p><strong>Date:</strong> <?= $ticket['show_date'] ?> | <strong>Time:</strong> <?= $ticket['time'] ?></p>
                        <p><strong>Studio:</strong> <?= $ticket['studio_name'] ?></p>
                        <p><strong>Seats:</strong> <?= $ticket['seats'] ?></p>
                        <p><strong>Total:</strong> Rp<?= number_format($ticket['total_price'], 0, ',', '.') ?></p>
                        <p><strong>Payment:</strong> <?= $ticket['payment_method'] ?> (<?= $ticket['payment_status'] ?>)</p>
                    </div>
                    <div class="ticket-footer">
                        Booking ID: <?= $ticket['booking_id'] ?> | Booked on <?= $ticket['booking_date'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.container {
    background: #1a1a1a;
    padding: 30px;
    border-radius: 15px;
    max-width: 900px;
    margin: 40px auto;
    color: white;
    font-family: 'Segoe UI', sans-serif;
}
h2 {
    color: #ff4d4d;
    margin-bottom: 25px;
    text-align: center;
}
.ticket-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.ticket-card {
    border: 2px dashed #ff4d4d;
    background: #262626;
    border-radius: 15px;
    padding: 20px;
    position: relative;
}
.ticket-header {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #ffcc00;
}
.ticket-body p {
    margin: 6px 0;
}
.ticket-footer {
    margin-top: 12px;
    font-size: 12px;
    color: #aaa;
    border-top: 1px solid #444;
    padding-top: 8px;
}
</style>

<?php include 'footer.php'; ?>
