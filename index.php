 <?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

try {
    // Fetch Top 3 Movies berdasarkan booking_count DESC
     $stmt_top3 = $pdo->prepare("SELECT movie_id, title, poster_url FROM Movie ORDER BY movie_id DESC LIMIT 3");
    $stmt_top3->execute();
    $top3_movies = $stmt_top3->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

?>

<?php include 'header.php'; ?>

<style>
.section {
    padding: 40px 20px;
    text-align: center;
}
.section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    margin-bottom: 30px;
    color: #f5c518;
}
.movie-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 30px;
}
.movie-card {
    background: #1c1c1c;
    border-radius: 12px;
    overflow: hidden;
    width: 200px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.7);
    transition: transform 0.3s;
}
.movie-card:hover {
    transform: scale(1.05);
}
.movie-card img {
    width: 100%;
    height: 300px;
    object-fit: cover;
}
.movie-card-title {
    padding: 10px;
    font-weight: bold;
}
.number-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #d90429;
    color: white;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 50%;
}
</style>

<!-- Top 3 Movies -->
 <div class="section">
    <h2>Top 3 Movies</h2>
    <div class="movie-grid">
        <?php $rank = 1; ?>
        <?php foreach ($top3_movies as $movie): ?>
            <div class="movie-card" style="position:relative;">
                <div class="number-badge"><?php echo $rank++; ?></div>
                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="movie-card-title"><?php echo htmlspecialchars($movie['title']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Coming Soon -->
<div class="section">
    <h2>Coming Soon</h2>
    <div class="movie-grid">
        <?php 
        // Dummy Coming Soon List (gunakan file lokal di folder images)
        $dummy_coming_soon = [
            ["title" => "Cleopatra", "poster_url" => "clptr.jpg"],
            ["title" => "The Nun 2", "poster_url" => "thenun2.jpg"],
            ["title" => "Avengers", "poster_url" => "avg.jpg"]
        ];

        foreach ($dummy_coming_soon as $movie): ?>
            <div class="movie-card">
                <img src="images/<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="movie-card-title"><?php echo htmlspecialchars($movie['title']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<a href="admin_login.php" style="
    position: fixed;
    bottom: 10px;
    right: 15px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
    z-index: 999;
">Admin Login</a>


<?php include 'footer.php'; ?>
