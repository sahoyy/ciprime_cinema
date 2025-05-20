 <?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    die("Movie ID not specified.");
}

$movie_id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM Movie WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        die("Movie not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<?php include 'header.php'; ?>

<!-- Inline CSS khusus halaman ini -->
<style>
    .movie-detail-container {
        padding: 30px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        gap: 40px;
    }

    .movie-poster {
        width: 300px;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    }

    .movie-info {
        max-width: 500px;
        color: #fff;
    }

    .movie-info h2 {
        color: #f5c518;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .movie-info p {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #f5c518;
        color: #000;
        text-decoration: none;
        font-weight: bold;
        border-radius: 8px;
        margin-top: 20px;
        transition: 0.3s;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .btn:hover {
        background-color: #e0b000;
        color: #111;
    }
</style>

<div class="movie-detail-container">
    <div>
        <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
             alt="<?php echo htmlspecialchars($movie['title']); ?>" 
             class="movie-poster">
    </div>

    <div class="movie-info">
        <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($movie['duration']); ?> minutes</p>
        <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie['rating']); ?></p>
        <p><strong>Synopsis:</strong><br><?php echo nl2br(htmlspecialchars($movie['synopsis'])); ?></p>
        <a href="schedule.php?movie_id=<?= $movie_id ?>" class="btn">Book Now</a>
    </div>
</div>

<?php include 'footer.php'; ?>
