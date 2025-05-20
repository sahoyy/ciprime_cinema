 <?php
require_once 'config.php';
session_start();

try {
    // Ambil semua movie yang ada di Schedule (tanpa filter tanggal)
    $stmt = $pdo->prepare("
        SELECT DISTINCT 
            m.movie_id, 
            m.title, 
            m.poster_url
        FROM Schedule s
        JOIN Movie m ON s.movie_id = m.movie_id
        ORDER BY m.movie_id
        LIMIT 5
    ");
    $stmt->execute();
    $now_showing = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<?php include 'header.php'; ?>

<style>
.movie-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
}
.movie-card {
    background: #1c1c1c;
    width: 220px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
    text-align: center;
}
.movie-card img {
    width: 100%;
    height: 320px;
    object-fit: cover;
}
.movie-info {
    padding: 10px;
    font-size: 14px;
    color: #fff;
}
.movie-info button {
    margin-top: 10px;
    background: #e50914;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 8px;
    cursor: pointer;
}
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 9999;
}
.modal-content {
  background: white; padding: 30px; border-radius: 12px; width: 300px; text-align: center;
}
.modal-content button {
  margin: 10px; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;
}
</style>

<h2 style="text-align:center;">Now Showing</h2>

<div class="movie-grid">
    <?php foreach ($now_showing as $movie): ?>
        <div class="movie-card">
            <a href="movie.php?id=<?php echo $movie['movie_id']; ?>">
                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
            </a>
            <div class="movie-info">
                <strong><?php echo htmlspecialchars($movie['title']); ?></strong><br>
                <small>Click for details & schedule!</small><br>
                <button onclick="handleBooking(<?php echo $movie['movie_id']; ?>)">Book Now</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- MODAL LOGIN REGISTER -->
<div class="modal-overlay" id="loginModal">
  <div class="modal-content">
    <h3 style="margin-bottom: 15px;">Already have an account?</h3>
    <button onclick="window.location.href='login.php'">Login</button>
    <h3 style="margin: 20px 0 10px;">New here?</h3>
    <button onclick="window.location.href='register.php'">Register</button>
    <br><br>
    <button onclick="closeModal()" style="background: #ccc;">Cancel</button>
  </div>
</div>

 <script>
const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

function handleBooking(movieId) {
    if (isLoggedIn) {
        window.location.href = "movie.php?id=" + movieId;
    } else {
        window.location.href = "login.php";
    }
}
</script>

<?php include 'footer.php'; ?>
