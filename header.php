 <!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ciprime Cinema</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom, #0f0f0f, #2c2c2c);
            color: white;
        }
        header {
            background: linear-gradient(to right, #1b1b1b, #333);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #f5c518;
        }
        .top-buttons button {
            margin-left: 10px;
            background: #d90429;
            border: none;
            padding: 10px 16px;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .main-nav {
            background: #111;
            padding: 10px;
            text-align: center;
        }
        .main-nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .main-nav a:hover {
            color: #f5c518;
        }
        .sidebar-left {
            position: fixed;
            top: 150px;
            left: 0;
            width: 50px;
            background-color: #111;
            padding: 10px 0;
            text-align: center;
            border-radius: 0 10px 10px 0;
        }
        .sidebar-left a {
            color: white;
            display: block;
            margin: 15px 0;
            font-size: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar Sosial Media -->
<div class="sidebar-left">
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-youtube"></i></a>
    <a href="#"><i class="fab fa-facebook-f"></i></a>
</div>

<!-- Header -->
<header>
    <div class="logo">CIPRIME</div>
    <div class="top-buttons">
        <a href="login.php"><button>Login</button></a>
        <a href="register.php"><button>Sign Up</button></a>
    </div>
</header>

<!-- Navigation -->
 <!-- Navigation -->
<div class="main-nav">
    <a href="index.php">HOME</a>
    <a href="nowshowing.php">NOW SHOWING</a>
    <a href="seat_selection.php">SEAT SELECTION</a>
    <a href="submit_payment.php">PAYMENT</a>
    <a href="my_tickets.php">TICKETING INFO</a>
</div>

