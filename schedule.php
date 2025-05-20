 <?php
require 'config.php';

if (!isset($_GET['movie_id']) || empty($_GET['movie_id'])) {
    die("Movie ID is missing.");
}

$movie_id = $_GET['movie_id'];

$stmt = $pdo->prepare("SELECT * FROM Movie WHERE movie_id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    die("Movie not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($movie['title']) ?> - Schedule</title>
    <style>
        body {
            background: linear-gradient(to right, #1c1c1c, #2e2e2e);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            background: #121212;
            padding: 30px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.2);
        }
        h2 {
            color: #ff4d4d;
            margin-bottom: 10px;
        }
        .movie-info p {
            margin: 5px 0;
        }
        .schedule-list {
            list-style: none;
            padding: 0;
        }
        .schedule-item {
            background: #1e1e1e;
            border-left: 5px solid #ff4d4d;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .schedule-text {
            font-size: 16px;
        }
        .btn {
            background-color: #0077ff;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #005fcc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($movie['title']) ?></h2>
        <div class="movie-info">
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
            <p><strong>Duration:</strong> <?= htmlspecialchars($movie['duration']) ?> mins</p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($movie['rating']) ?></p>
            <p><strong>Synopsis:</strong> <?= nl2br(htmlspecialchars($movie['synopsis'])) ?></p>
        </div>

        <h3>Available Schedules</h3>
        <ul class="schedule-list">
            <?php
            $stmt2 = $pdo->prepare("SELECT * FROM Schedule WHERE movie_id = ?");
            $stmt2->execute([$movie_id]);
            $schedules = $stmt2->fetchAll();

            if ($schedules) {
                foreach ($schedules as $schedule) {
                    echo "<li class='schedule-item'>";
                    echo "<div class='schedule-text'>";
                    echo "Date: <strong>" . htmlspecialchars($schedule['date']) . "</strong> | ";
                    echo "Time: <strong>" . htmlspecialchars($schedule['time']) . "</strong> | ";
                    echo "Studio: <strong>" . htmlspecialchars($schedule['studio_id']) . "</strong> | ";
                    echo "Price: <strong>Rp " . number_format($schedule['price'], 2, ',', '.') . "</strong>";
                    echo "</div>";
                    echo "<a class='btn' href='seat_selection.php?schedule_id=" . $schedule['schedule_id'] . "'>Select Seat</a>";
                    echo "</li>";
                }
            } else {
                echo "<li class='schedule-item'>No schedules available.</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
