<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';
$update_mode = false;
$id = ''; $flight_number = ''; $airline = ''; $from_city = ''; $to_city = ''; $departure_time = ''; $arrival_time = ''; $duration = ''; $price = '';

// ১. CREATE (নতুন ফ্লাইট যোগ করা)
if (isset($_POST['add_flight'])) {
    $flight_number = $_POST['flight_number'];
    $airline = $_POST['airline'];
    $from_city = $_POST['from_city'];
    $to_city = $_POST['to_city'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("INSERT INTO flights (flight_number, airline, from_city, to_city, departure_time, arrival_time, duration, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$flight_number, $airline, $from_city, $to_city, $departure_time, $arrival_time, $duration, $price]);
    $message = "<p style='color: green; font-weight:bold;'>✓ Flight Added Successfully!</p>";
}

// ২. DELETE (ফ্লাইট ডিলিট করা)
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM flights WHERE id = ?");
    $stmt->execute([$del_id]);
    header("Location: admin.php");
    exit;
}

// ৩. UPDATE এর জন্য ডেটা ফেচ করা
if (isset($_GET['edit'])) {
    $update_mode = true;
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->execute([$edit_id]);
    $flight = $stmt->fetch();
    
    if ($flight) {
        $id = $flight['id'];
        $flight_number = $flight['flight_number'];
        $airline = $flight['airline'];
        $from_city = $flight['from_city'];
        $to_city = $flight['to_city'];
        $departure_time = $flight['departure_time'];
        $arrival_time = $flight['arrival_time'];
        $duration = $flight['duration'];
        $price = $flight['price'];
    }
}

// ৪. UPDATE সেভ করা
if (isset($_POST['update_flight'])) {
    $id = $_POST['id'];
    $flight_number = $_POST['flight_number'];
    $airline = $_POST['airline'];
    $from_city = $_POST['from_city'];
    $to_city = $_POST['to_city'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("UPDATE flights SET flight_number=?, airline=?, from_city=?, to_city=?, departure_time=?, arrival_time=?, duration=?, price=? WHERE id=?");
    $stmt->execute([$flight_number, $airline, $from_city, $to_city, $departure_time, $arrival_time, $duration, $price, $id]);
    header("Location: admin.php");
    exit;
}

// সব ফ্লাইটের ডেটা আনা
$flights = $pdo->query("SELECT * FROM flights")->fetchAll();

// নতুন ফিচার: ডেটাবেস জয়েন কুয়েরি দিয়ে সব প্যাসেঞ্জারের বুকিং হিস্ট্রি নিয়ে আসা
$bookings = $pdo->query("
    SELECT b.passenger_name, b.ticket_number, b.booking_date, f.flight_number, f.airline, f.from_city, f.to_city 
    FROM bookings b 
    JOIN flights f ON b.flight_id = f.id 
    ORDER BY b.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - VromonVibe</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .dashboard { max-width: 1100px; margin: 0 auto; background: white; padding: 35px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        h2, h3 { color: #1A73E8; font-weight: 700; }
        h3 { border-bottom: 2px solid #f1f3f4; padding-bottom: 10px; margin-top: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .form-group input { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        .btn { background: #1A73E8; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px; font-family: 'Poppins', sans-serif; }
        .btn-danger { background: #d93025; text-decoration: none; padding: 6px 12px; border-radius: 6px; color: white; font-size: 13px; }
        .btn-edit { background: #f4b400; color: black; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 14px; border: 1px solid #eee; text-align: left; font-size: 14px; }
        th { background-color: #f8fafc; color: #1A73E8; font-weight: 600; }
        .nav-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #1A73E8; font-weight: bold; }
        .pnr-badge { background: #ffe4e6; color: #e11d48; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
<div class="dashboard">
    <a href="index.php" class="nav-back">← Back to Homepage</a>
    <h2>VromonVibe Management Dashboard</h2>
    <?php echo $message; ?>
    
    <form method="POST" class="form-group">
        <h3><?php echo $update_mode ? "✏ Edit Flight Details" : "➕ Add New Flight Route"; ?></h3>
        <div class="form-grid">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="text" name="flight_number" placeholder="Flight Number (e.g. VQ-901)" value="<?php echo htmlspecialchars($flight_number); ?>" required>
            <input type="text" name="airline" placeholder="Airline Name (e.g. NOVOAIR)" value="<?php echo htmlspecialchars($airline); ?>" required>
            <input type="text" name="from_city" placeholder="From City" value="<?php echo htmlspecialchars($from_city); ?>" required>
            <input type="text" name="to_city" placeholder="To City" value="<?php echo htmlspecialchars($to_city); ?>" required>
            <input type="time" name="departure_time" value="<?php echo htmlspecialchars($departure_time); ?>" required>
            <input type="time" name="arrival_time" value="<?php echo htmlspecialchars($arrival_time); ?>" required>
            <input type="text" name="duration" placeholder="Duration (e.g. 1h 5m)" value="<?php echo htmlspecialchars($duration); ?>" required>
            <input type="number" step="0.01" name="price" placeholder="Fare (BDT)" value="<?php echo htmlspecialchars($price); ?>" required>
        </div>
        <?php if($update_mode): ?>
            <button type="submit" name="update_flight" class="btn" style="background:#f4b400; color:black;">Update Flight Route</button>
            <a href="admin.php" class="btn" style="background:#aaa; text-decoration:none; display:inline-block;">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_flight" class="btn">Save Flight Route</button>
        <?php endif; ?>
    </form>

    <!-- নতুন রিয়েল-টাইম বুকিং হিস্ট্রি সেকশন -->
    <h3>📋 Passenger Ticket Roster (Real-time Booking History)</h3>
    <?php if(count($bookings) == 0): ?>
        <p style="color: #666; font-style: italic; padding: 10px 0;">No tickets booked yet by any passenger.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Passenger Name</th>
                    <th>Ticket PNR</th>
                    <th>Flight Details</th>
                    <th>Route</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $book): ?>
                    <tr>
                        <td style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($book['passenger_name']); ?></td>
                        <td><span class="pnr-badge"><?php echo htmlspecialchars($book['ticket_number']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($book['airline']); ?></strong> (<?php echo htmlspecialchars($book['flight_number']); ?>)</td>
                        <td><?php echo htmlspecialchars($book['from_city']); ?> ➔ <?php echo htmlspecialchars($book['to_city']); ?></td>
                        <td style="color: #666; font-weight: 500;"><?php echo htmlspecialchars($book['booking_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>✈ Manage Current Flight Schedules</h3>
    <table>
        <thead><tr><th>Flight No</th><th>Airline</th><th>Route</th><th>Departure</th><th>Arrival</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach($flights as $fl): ?>
                <tr>
                    <td><code><?php echo htmlspecialchars($fl['flight_number']); ?></code></td>
                    <td><strong><?php echo htmlspecialchars($fl['airline']); ?></strong></td>
                    <td><?php echo htmlspecialchars($fl['from_city']); ?> ➔ <?php echo htmlspecialchars($fl['to_city']); ?></td>
                    <td><?php echo htmlspecialchars($fl['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($fl['arrival_time']); ?></td>
                    <td style="font-weight: 600; color:#1A73E8;">BDT <?php echo number_format($fl['price']); ?></td>
                    <td>
                        <a href="admin.php?edit=<?php echo $fl['id']; ?>" class="btn-edit">Edit</a>
                        <a href="admin.php?delete=<?php echo $fl['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this route?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>