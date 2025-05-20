 <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Validasi input
if (!isset($_POST['schedule_id'], $_POST['selected_seats'], $_POST['payment_method'], $_POST['total_price'])) {
    die("invalid data, return to previous page.");
}

$user_id = $_SESSION['user_id'];
$schedule_id = $_POST['schedule_id'];
$selected_seats = $_POST['selected_seats'];
$payment_method = $_POST['payment_method'];
$total_price = $_POST['total_price'];
$seat_ids = explode(',', $selected_seats);

try {
    // Mulai transaksi
    $pdo->beginTransaction();
    
    // DEBUG: Menampilkan informasi awal
    error_log("Starting transaction with schedule_id: $schedule_id, user_id: $user_id, seats: $selected_seats");
    
    // LANGKAH 1: Buat Booking terlebih dahulu
    // Booking_id akan di-generate otomatis oleh database (AUTO_INCREMENT)
    
    // Periksa struktur tabel Booking
    $stmt = $pdo->query("DESCRIBE Booking");
    $booking_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    error_log("Booking columns: " . implode(", ", $booking_columns));
    
    // Siapkan query Booking berdasarkan kolom yang tersedia
    $booking_sql_columns = [];
    $booking_sql_placeholders = [];
    $booking_params = [];
    
    // Cek kolom yang ada di tabel Booking
    if (in_array("user_id", $booking_columns)) {
        $booking_sql_columns[] = "user_id";
        $booking_sql_placeholders[] = "?";
        $booking_params[] = $user_id;
    }
    
    if (in_array("schedule_id", $booking_columns)) {
        $booking_sql_columns[] = "schedule_id";
        $booking_sql_placeholders[] = "?";
        $booking_params[] = $schedule_id;
    }
    
    if (in_array("booking_date", $booking_columns)) {
        $booking_sql_columns[] = "booking_date";
        $booking_sql_placeholders[] = "NOW()";
    }
    
    if (in_array("total_price", $booking_columns)) {
        $booking_sql_columns[] = "total_price";
        $booking_sql_placeholders[] = "?";
        $booking_params[] = $total_price;
    }
    
    // Tambahkan status jika kolom tersebut ada
    if (in_array("status", $booking_columns)) {
        $booking_sql_columns[] = "status";
        $booking_sql_placeholders[] = "?";
        $booking_params[] = "Confirmed";
    }
    
    // Eksekusi query Booking
    $booking_sql = "INSERT INTO Booking (" . implode(", ", $booking_sql_columns) . ") VALUES (" . implode(", ", $booking_sql_placeholders) . ")";
    error_log("Booking SQL: $booking_sql");
    error_log("Booking params: " . implode(", ", $booking_params));
    
    $booking_stmt = $pdo->prepare($booking_sql);
    $result = $booking_stmt->execute($booking_params);
    
    if (!$result) {
        throw new PDOException("Failed to insert into Booking: " . implode(", ", $booking_stmt->errorInfo()));
    }
    
    // Dapatkan booking_id yang baru saja dibuat
    $booking_id = $pdo->lastInsertId();
    error_log("New booking created with ID: $booking_id");
    
    if (!$booking_id) {
        throw new PDOException("Failed to get last insert ID for booking");
    }
    
    // LANGKAH 2: Masukkan detail kursi ke Booking_Detail
    // Periksa struktur tabel Booking_Detail
    $stmt = $pdo->query("DESCRIBE Booking_Detail");
    $booking_detail_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Tambahkan detail untuk setiap kursi
    foreach ($seat_ids as $seat_id) {
        $booking_detail_sql_columns = [];
        $booking_detail_sql_placeholders = [];
        $booking_detail_params = [];
        
        if (in_array("booking_id", $booking_detail_columns)) {
            $booking_detail_sql_columns[] = "booking_id";
            $booking_detail_sql_placeholders[] = "?";
            $booking_detail_params[] = $booking_id;
        }
        
        if (in_array("seat_id", $booking_detail_columns)) {
            $booking_detail_sql_columns[] = "seat_id";
            $booking_detail_sql_placeholders[] = "?";
            $booking_detail_params[] = $seat_id;
        }
        
        // Tambahkan price_per_seat jika kolom tersebut ada
        if (in_array("price_per_seat", $booking_detail_columns)) {
            // Dapatkan harga dari schedule
            $price_stmt = $pdo->prepare("SELECT price FROM Schedule WHERE schedule_id = ?");
            $price_stmt->execute([$schedule_id]);
            $price_result = $price_stmt->fetch();
            
            if ($price_result) {
                $booking_detail_sql_columns[] = "price_per_seat";
                $booking_detail_sql_placeholders[] = "?";
                $booking_detail_params[] = $price_result['price'];
            }
        }
        
        // Eksekusi query Booking_Detail
        $booking_detail_sql = "INSERT INTO Booking_Detail (" . implode(", ", $booking_detail_sql_columns) . ") VALUES (" . implode(", ", $booking_detail_sql_placeholders) . ")";
        $booking_detail_stmt = $pdo->prepare($booking_detail_sql);
        $booking_detail_stmt->execute($booking_detail_params);
        
        // Update status kursi jika diperlukan (tidak ada kolom is_available di database, jadi kita lewati)
    }
    
    // LANGKAH 3: Buat Payment dengan mengacu pada booking_id
    // Periksa struktur tabel Payment
    $stmt = $pdo->query("DESCRIBE Payment");
    $payment_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    error_log("Payment columns: " . implode(", ", $payment_columns));
    
    // Siapkan query Payment berdasarkan kolom yang tersedia
    $payment_sql_columns = [];
    $payment_sql_placeholders = [];
    $payment_params = [];
    
    // Booking ID sangat penting karena ada foreign key constraint
    if (in_array("booking_id", $payment_columns)) {
        $payment_sql_columns[] = "booking_id";
        $payment_sql_placeholders[] = "?";
        $payment_params[] = $booking_id;
    }
    
    // Cek kolom yang ada di tabel Payment
    if (in_array("user_id", $payment_columns)) {
        $payment_sql_columns[] = "user_id";
        $payment_sql_placeholders[] = "?";
        $payment_params[] = $user_id;
    }
    
    if (in_array("amount", $payment_columns)) {
        $payment_sql_columns[] = "amount";
        $payment_sql_placeholders[] = "?";
        $payment_params[] = $total_price;
    }
    
    if (in_array("payment_method", $payment_columns)) {
        $payment_sql_columns[] = "payment_method";
        $payment_sql_placeholders[] = "?";
        $payment_params[] = $payment_method;
    }
    
    if (in_array("status", $payment_columns)) {
        $payment_sql_columns[] = "status";
        $payment_sql_placeholders[] = "?";
        $payment_params[] = "Paid";
    }
    
    if (in_array("payment_date", $payment_columns)) {
        $payment_sql_columns[] = "payment_date";
        $payment_sql_placeholders[] = "NOW()";
    }
    
    // Eksekusi query Payment
    $payment_sql = "INSERT INTO Payment (" . implode(", ", $payment_sql_columns) . ") VALUES (" . implode(", ", $payment_sql_placeholders) . ")";
    error_log("Payment SQL: $payment_sql");
    error_log("Payment params: " . implode(", ", $payment_params));
    
    $payment_stmt = $pdo->prepare($payment_sql);
    $result = $payment_stmt->execute($payment_params);
    
    if (!$result) {
        throw new PDOException("Failed to insert into Payment: " . implode(", ", $payment_stmt->errorInfo()));
    }
    
    // Dapatkan payment_id yang baru saja dibuat
    $payment_id = $pdo->lastInsertId();
    error_log("New payment created with ID: $payment_id");
    
    // Commit transaksi
    $pdo->commit();
    
    // Ambil data untuk ditampilkan di halaman konfirmasi
    $infoStmt = $pdo->prepare("SELECT s.date, s.time, m.title, st.studio_name 
                            FROM Schedule s
                            JOIN Movie m ON s.movie_id = m.movie_id
                            JOIN Studio st ON s.studio_id = st.studio_id
                            WHERE s.schedule_id = ?");
    $infoStmt->execute([$schedule_id]);
    $movieInfo = $infoStmt->fetch();
    
    // Ambil nomor kursi
    $inPlaceholders = implode(',', array_fill(0, count($seat_ids), '?'));
    $seatStmt = $pdo->prepare("SELECT seat_number FROM Seat WHERE seat_id IN ($inPlaceholders)");
    $seatStmt->execute($seat_ids);
    $seatNumbers = $seatStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    // Rollback jika ada error
    $pdo->rollBack();
    die("Error processing payment: " . $e->getMessage());
}

// Include header
include 'header.php';
?>

<div class="container">
    <div class="success-message">
        <i class="fa fa-check-circle"></i>
        <h2>Payment Successful!</h2>
    </div>
    
    <div class="ticket-info">
        <h3>Ticket Details</h3>
        <p><strong>Booking ID:</strong> <?= $booking_id ?></p>
        <p><strong>Movie:</strong> <?= htmlspecialchars($movieInfo['title']) ?></p>
        <p><strong>Date:</strong> <?= $movieInfo['date'] ?> | <strong>time:</strong> <?= $movieInfo['time'] ?></p>
        <p><strong>Studio:</strong> <?= $movieInfo['studio_name'] ?></p>
        <p><strong>Seat:</strong> <?= implode(', ', $seatNumbers) ?></p>
        <p><strong>Total Payment:</strong> Rp<?= number_format($total_price, 0, ',', '.') ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment_method) ?></p>
    </div>
    
    <div class="action-buttons">
        <a href="my_tickets.php" class="btn">View All Tickets</a>
        <a href="index.php" class="btn">Back to Home</a>
    </div>
</div>

<style>
    .container {
        background: #1a1a1a;
        padding: 25px;
        border-radius: 15px;
        max-width: 700px;
        margin: 50px auto;
        color: white;
        box-shadow: 0 0 20px rgba(0,0,0,0.6);
        text-align: center;
    }
    
    .success-message {
        margin-bottom: 30px;
    }
    
    .success-message i {
        font-size: 80px;
        color: #4CAF50;
        margin-bottom: 20px;
    }
    
    .success-message h2 {
        color: #4CAF50;
    }
    
    .ticket-info {
        background: #262626;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        text-align: left;
    }
    
    .ticket-info h3 {
        color: #ff4d4d;
        margin-top: 0;
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 1px solid #444;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px;
        background-color: #ff4d4d;
        border: none;
        color: white;
        text-decoration: none;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
    }
    
    .btn:hover {
        background-color: #ff3333;
    }
</style>

<?php include 'footer.php'; ?>