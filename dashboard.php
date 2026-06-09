<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 🛠️ Explicit Aliasing shob column er jonno jate kono collision na hoy
$stmt = $pdo->prepare("
    SELECT 
        b.id AS b_id,
        b.ticket_number AS b_pnr,
        b.passenger_name AS b_passenger,
        b.booking_date AS b_date,
        f.from_city AS f_from,
        f.to_city AS f_to,
        f.price AS f_price,
        f.airline AS f_airline
    FROM bookings b
    LEFT JOIN flights f ON b.flight_id = f.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
");
$stmt->execute([$user_id]);
$my_bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard - VromonVibe</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .dashboard-container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h2 { color: #1e3a8a; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        .welcome-text { font-size: 18px; color: #4b5563; margin-bottom: 30px; }
        .ticket-card { background: #fff; border: 1px dashed #3b82f6; border-left: 8px solid #3b82f6; padding: 20px; margin-bottom: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .ticket-details h3 { margin: 0 0 10px 0; color: #1f2937; }
        .pnr { font-weight: bold; color: #2563eb; background: #dbeafe; padding: 4px 8px; border-radius: 4px; font-size: 16px; }
        .btn-home { background: #1e3a8a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 20px; font-weight: bold; }
        .btn-home:hover { background: #1557b0; }
        .no-tickets { text-align: center; color: #9ca3af; padding: 30px 0; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome to Your Dashboard ✈️</h2>
    <p class="welcome-text">Hello, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Passenger'); ?></strong>! Here are your booked e-tickets:</p>

        <?php if (empty($my_bookings)): ?>
            <p class="no-tickets">You haven't booked any flights yet. Happy traveling!</p>
        <?php else: ?>
            <?php foreach ($my_bookings as $booking): 
                $flight_from = $booking['f_from'] ?? 'N/A';
                $flight_to = $booking['f_to'] ?? 'N/A';
                $passenger = $booking['b_passenger'] ?? $_SESSION['user_name'];
                $pnr = $booking['b_pnr'] ?? 'N/A';
                $total_price = $booking['f_price'] ?? '0';
            ?>
                <div class="ticket-card">
                    <div class="ticket-details">
                        <h3>✈ Flight: <?php echo htmlspecialchars($flight_from); ?> ➔ <?php echo htmlspecialchars($flight_to); ?></h3>
                        <p>Passenger: <strong><?php echo htmlspecialchars($passenger); ?></strong> (Airline: <?php echo htmlspecialchars($booking['f_airline'] ?? 'Domestic'); ?>)</p>
                        <p>Total Paid: <strong style="color: #1A73E8;"><?php echo is_numeric($total_price) ? number_format((float)$total_price) : htmlspecialchars($total_price); ?> BDT</strong></p>
                    </div>
                    <div>
                        <span class="pnr">PNR: <?php echo htmlspecialchars($pnr); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <a href="index.php" class="btn-home">⬅ Back to Home</a>
</div>

</body>
</html>