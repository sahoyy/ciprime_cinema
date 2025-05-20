 <?php
require_once 'config.php';

if (!isset($_GET['schedule_id'])) {
    die("Schedule ID is missing.");
}

$schedule_id = $_GET['schedule_id'];

// Ambil detail jadwal dan film
$stmt = $pdo->prepare("
    SELECT s.*, m.title, m.genre, m.duration, m.rating, m.synopsis, st.studio_name
    FROM Schedule s
    JOIN Movie m ON s.movie_id = m.movie_id
    JOIN Studio st ON s.studio_id = st.studio_id
    WHERE s.schedule_id = ?
");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    die("Schedule not found.");
}

// Ambil semua kursi dari studio terkait
$seatStmt = $pdo->prepare("
    SELECT * FROM Seat 
    WHERE studio_id = ? 
    ORDER BY LEFT(seat_number, 1), CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)
");
$seatStmt->execute([$schedule['studio_id']]);
$seats = $seatStmt->fetchAll();

// Ambil kursi yang sudah dibooking untuk jadwal ini dan studio ini
$bookedStmt = $pdo->prepare("
    SELECT s.seat_id
    FROM Booking_Detail bd
    JOIN Booking b ON bd.booking_id = b.booking_id
    JOIN Seat s ON bd.seat_id = s.seat_id
    WHERE b.schedule_id = ? AND s.studio_id = ?
");
$bookedStmt->execute([$schedule_id, $schedule['studio_id']]);
$bookedSeats = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Select Your Seat</h2>
    <p><strong>Movie:</strong> <?= htmlspecialchars($schedule['title']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($schedule['date']) ?> |
       <strong>Time:</strong> <?= htmlspecialchars($schedule['time']) ?></p>
    <p><strong>Studio:</strong> <?= htmlspecialchars($schedule['studio_name']) ?></p>

    <form action="checkout.php" method="post" id="seatForm">
        <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">

        <div class="seat-grid">
            <?php foreach ($seats as $seat): ?>
                <?php
                    $isBooked = in_array($seat['seat_id'], $bookedSeats);
                    $seatNumber = htmlspecialchars($seat['seat_number']);
                ?>
                <div class="seat <?= $isBooked ? 'booked' : '' ?>"
                     <?= !$isBooked ? 'data-seat-id="'.$seat['seat_id'].'" data-seat-number="'.$seatNumber.'"' : '' ?>>
                    <?= $seatNumber ?>
                </div>
            <?php endforeach; ?>
        </div>

        <input type="hidden" name="selected_seats" id="selectedSeats">
        <button type="submit" class="submit-btn">Proceed to Checkout</button>
    </form>
</div>

<style>
    body {
        background: #101010;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
        padding: 30px;
    }
    .container {
        background: #1a1a1a;
        padding: 25px;
        border-radius: 15px;
        max-width: 800px;
        margin: auto;
        box-shadow: 0 0 20px rgba(0,0,0,0.6);
    }
    h2 {
        color: #ff4d4d;
    }
    .seat-grid {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 10px;
        margin-top: 20px;
        justify-items: center;
    }
    .seat {
        padding: 12px;
        width: 40px;
        text-align: center;
        border-radius: 5px;
        background-color: #0077ff;
        color: white;
        cursor: pointer;
        transition: background 0.3s;
    }
    .seat:hover {
        background-color: #005fcc;
    }
    .seat.selected {
        background-color: #00cc66;
    }
    .seat.booked {
        background-color: #777;
        pointer-events: none;
    }
    .submit-btn {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #ff4d4d;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
    }
</style>

<script>
    const seats = document.querySelectorAll('.seat:not(.booked)');
    const selectedSeatsInput = document.getElementById('selectedSeats');
    const selectedSeatIds = [];

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            const seatId = seat.getAttribute('data-seat-id');
            seat.classList.toggle('selected');

            if (selectedSeatIds.includes(seatId)) {
                selectedSeatIds.splice(selectedSeatIds.indexOf(seatId), 1);
            } else {
                selectedSeatIds.push(seatId);
            }

            selectedSeatsInput.value = selectedSeatIds.join(',');
        });
    });
</script>

<?php include 'footer.php'; ?>
