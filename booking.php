<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['schedule_id'])) {
    echo "Schedule ID is missing.";
    exit();
}

$schedule_id = $_GET['schedule_id'];
$user_id = $_SESSION['user_id'];

// Ambil detail jadwal
$sql = "SELECT s.schedule_id, s.date, s.time, s.price, 
               m.title, m.poster_url, m.genre, m.duration, m.rating, m.synopsis,
               st.studio_name 
        FROM Schedule s
        JOIN Movie m ON s.movie_id = m.movie_id
        JOIN Studio st ON s.studio_id = st.studio_id
        WHERE s.schedule_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_assoc();

if (!$schedule) {
    echo "Schedule not found.";
    exit();
}

// Ambil kursi yang tersedia
$seat_query = "SELECT s.seat_id, s.seat_number 
               FROM Seat s
               WHERE s.studio_id = (
                    SELECT studio_id FROM Schedule WHERE schedule_id = ?
               )
               AND s.seat_id NOT IN (
                    SELECT seat_id FROM Booking_Detail bd
                    JOIN Booking b ON bd.booking_id = b.booking_id
                    WHERE b.schedule_id = ? AND b.status = 'Confirmed'
               )";
$stmt = $conn->prepare($seat_query);
$stmt->bind_param("ii", $schedule_id, $schedule_id);
$stmt->execute();
$available_seats = $stmt->get_result();

$booking_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats = $_POST['seats'] ?? [];

    if (empty($selected_seats)) {
        $error = "Please select at least one seat.";
    } else {
        $total_price = count($selected_seats) * $schedule['price'];

        // Insert Booking
        $insert_booking = $conn->prepare("INSERT INTO Booking (user_id, schedule_id, booking_date, total_price, status) VALUES (?, ?, NOW(), ?, 'Confirmed')");
        $insert_booking->bind_param("iid", $user_id, $schedule_id, $total_price);
        $insert_booking->execute();
        $booking_id = $conn->insert_id;

        // Insert Booking_Detail
        $detail_stmt = $conn->prepare("INSERT INTO Booking_Detail (booking_id, seat_id, price_per_seat) VALUES (?, ?, ?)");
        foreach ($selected_seats as $seat_id) {
            $detail_stmt->bind_param("iid", $booking_id, $seat_id, $schedule['price']);
            $detail_stmt->execute();
        }

        header("Location: payment.php?booking_id=$booking_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket - Ciprime</title>
    <style>
        body {
            background: linear-gradient(to right, #1b1f3b, #2e1c2b);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #292c3e;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(255, 0, 60, 0.4);
        }
        h2 {
            text-align: center;
            color: #ff3366;
        }
        .movie-info {
            display: flex;
            gap: 20px;
        }
        .poster {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255,255,255,0.2);
        }
        .details {
            flex: 1;
        }
        .details p {
            margin: 8px 0;
        }
        .seats {
            margin-top: 30px;
        }
        .seats label {
            display: inline-block;
            background: #1c1e2f;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 5px;
            cursor: pointer;
            border: 2px solid #ff3366;
        }
        .seats input[type="checkbox"] {
            display: none;
        }
        .seats input[type="checkbox"]:checked + span {
            background: #ff3366;
            color: white;
        }
        .submit-btn {
            margin-top: 20px;
            text-align: center;
        }
        .submit-btn button {
            background: #3366ff;
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
        }
        .submit-btn button:hover {
            background: #5588ff;
        }
        .error {
            color: #ff9999;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Booking - <?= htmlspecialchars($schedule['title']) ?></h2>

        <div class="movie-info">
            <img src="<?= htmlspecialchars($schedule['poster_url']) ?>" class="poster" alt="Poster">
            <div class="details">
                <p><strong>Genre:</strong> <?= $schedule['genre'] ?></p>
                <p><strong>Duration:</strong> <?= $schedule['duration'] ?> mins</p>
                <p><strong>Rating:</strong> <?= $schedule['rating'] ?></p>
                <p><strong>Studio:</strong> <?= $schedule['studio_name'] ?></p>
                <p><strong>Date:</strong> <?= $schedule['date'] ?> &nbsp;&nbsp; <strong>Time:</strong> <?= $schedule['time'] ?></p>
                <p><strong>Price per seat:</strong> Rp <?= number_format($schedule['price'], 0, ',', '.') ?></p>
            </div>
        </div>

        <form method="POST">
            <div class="seats">
                <h3>Select Your Seats</h3>
                <?php while ($seat = $available_seats->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="seats[]" value="<?= $seat['seat_id'] ?>">
                        <span><?= $seat['seat_number'] ?></span>
                    </label>
                <?php endwhile; ?>
            </div>
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <div class="submit-btn">
                <button type="submit">Confirm Booking</button>
            </div>
        </form>
    </div>
</body>
</html>
