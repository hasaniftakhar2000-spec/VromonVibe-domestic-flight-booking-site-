<?php
session_start();
require 'db.php';

// ১. সিকিউরিটি চেক: ইউজার লগইন না থাকলে বা অ্যাডমিন না হলে হোমপেজে পাঠিয়ে দেবে
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// 🛠️ ২. টিকিট ডিলিট করার অ্যাকশন হ্যান্ডলার
if (isset($_GET['delete_booking_id'])) {
    $booking_id = (int)$_GET['delete_booking_id'];
    $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$booking_id]);
    header("Location: admin.php?status=deleted");
    exit;
}

// 🛠️ ৩. নতুন ফ্লাইট অ্যাড করার অ্যাকশন হ্যান্ডলার
if (isset($_POST['add_flight'])) {
    $stmt = $pdo->prepare("INSERT INTO flights (airline, flight_number, from_city, to_city, departure_time, arrival_time, duration, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['airline'], 
        $_POST['flight_number'], 
        $_POST['from_city'], 
        $_POST['to_city'], 
        $_POST['departure_time'], 
        $_POST['arrival_time'], 
        $_POST['duration'], 
        $_POST['price']
    ]);
    header("Location: admin.php?status=flight_added");
    exit;
}

// 🛠️ ৪. সম্পূর্ণ ফ্লাইট ডিলিট করার অ্যাকশন হ্যান্ডলার
if (isset($_GET['delete_flight_id'])) {
    $flight_id = (int)$_GET['delete_flight_id'];
    $pdo->prepare("DELETE FROM flights WHERE id = ?")->execute([$flight_id]);
    header("Location: admin.php?status=flight_deleted");
    exit;
}

// 📊 ডেটাবেস থেকে সমস্ত বুকিং, ফ্লাইট এবং রেজিস্টার্ড ইউজারদের তথ্য তুলে আনা
$bookings = $pdo->query("
    SELECT 
        b.id AS b_id,
        b.ticket_number AS b_pnr,
        b.passenger_name AS b_passenger,
        b.seat_class AS b_class,
        b.total_price AS b_price,
        f.from_city AS f_from,
        f.to_city AS f_to,
        f.price AS f_price,
        f.airline AS f_airline
    FROM bookings b 
    LEFT JOIN flights f ON b.flight_id = f.id 
    ORDER BY b.id DESC
")->fetchAll();

$flights = $pdo->query("SELECT * FROM flights ORDER BY id DESC")->fetchAll();
$users = $pdo->query("SELECT id, name, email, role FROM users ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Extreme Panel - VromonVibe</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f9; padding: 20px; margin: 0; }
        .container { max-width: 1200px; margin: 20px auto; background: white; padding: 35px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        h2 { color: #1e3a8a; font-weight: 700; border-bottom: 3px solid #1A73E8; padding-bottom: 12px; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        h3 { color: #2c3e50; font-weight: 600; margin-top: 40px; margin-bottom: 15px; border-left: 5px solid #1A73E8; padding-left: 10px; }
        
        /* সাকসেস মেসেজ অ্যালার্ট */
        .alert { padding: 12px 18px; border-radius: 8px; font-weight: 500; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        
        /* টেবিল ডিজাইন */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #1A73E8; color: white; font-weight: 600; font-size: 14px; }
        tr:hover { background-color: #f8fafc; }
        
        /* বাটন ও ইনপুট স্টাইল */
        .btn-red { background: #ef4444; color: white; padding: 7px 14px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: 0.2s; }
        .btn-red:hover { background: #dc2626; }
        
        .form-container { background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px; }
        
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: #4b5563; margin-bottom: 5px; text-transform: uppercase; }
        input, select { padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 14px; outline: none; transition: 0.3s; }
        input:focus, select:focus { border-color: #1A73E8; box-shadow: 0 0 8px rgba(26,115,232,0.15); }
        
        button { background: #10b981; color: white; border: none; padding: 12px 24px; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 15px; font-family: 'Poppins', sans-serif; transition: 0.3s; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
        button:hover { background: #059669; transform: scale(1.02); }
        
        .pnr-badge { font-weight: bold; color: #2563eb; background: #dbeafe; padding: 4px 8px; border-radius: 4px; font-family: monospace; }
        .class-badge { background: #f0fdf4; color: #16a34a; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; border: 1px solid #bbf7d0; }
        .role-badge { background: #e2e8f0; color: #475569; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .btn-back { background: #4b5563; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 20px; font-weight: bold; font-size: 14px; }
        .btn-back:hover { background: #374151; }
        .no-data { text-align: center; color: #9ca3af; padding: 25px 0; font-style: italic; }
    </style>
</head>
<body>

<div class="container">
    <h2>👑 Admin Extreme Dashboard</h2>
    
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success">✓ Passenger flight ticket has been successfully cancelled and removed.</div>
        <?php elseif ($_GET['status'] == 'flight_added'): ?>
            <div class="alert alert-success">✓ New dynamic flight plan successfully added to the system!</div>
        <?php elseif ($_GET['status'] == 'flight_deleted'): ?>
            <div class="alert alert-success">✓ Dynamic flight schedule has been permanently removed from system.</div>
        <?php endif; ?>
    <?php endif; ?>
    
    <h3>✈️ Manage & Add Dynamic Flights</h3>
    <div class="form-container">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Airline Name</label>
                    <input type="text" name="airline" placeholder="e.g. US-Bangla Airlines" required>
                </div>
                <div class="form-group">
                    <label>Flight Number</label>
                    <input type="text" name="flight_number" placeholder="e.g. BS-141" required>
                </div>
                <div class="form-group">
                    <label>Departure City</label>
                    <input type="text" name="from_city" placeholder="e.g. Dhaka" required>
                </div>
                <div class="form-group">
                    <label>Arrival City</label>
                    <input type="text" name="to_city" placeholder="e.g. Cox's Bazar" required>
                </div>
                <div class="form-group">
                    <label>Departure Time</label>
                    <input type="time" name="departure_time" required>
                </div>
                <div class="form-group">
                    <label>Arrival Time</label>
                    <input type="time" name="arrival_time" required>
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" placeholder="e.g. 1h 05m" required>
                </div>
                <div class="form-group">
                    <label>Base Price (BDT)</label>
                    <input type="number" name="price" placeholder="e.g. 4500" required>
                </div>
            </div>
            <button type="submit" name="add_flight">➕ Add New Dynamic Flight</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Airline Details</th>
                <th>Route</th>
                <th>Schedule</th>
                <th>Base Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($flights)): ?>
                <tr><td colspan="6" class="no-data">No active flights available in the system.</td></tr>
            <?php else: ?>
                <?php foreach ($flights as $f): ?>
                <tr>
                    <td><b>#<?= $f['id'] ?></b></td>
                    <td><strong><?= htmlspecialchars($f['airline']) ?></strong><br><small style="color:#64748b"><?= htmlspecialchars($f['flight_number']) ?></small></td>
                    <td><?= htmlspecialchars($f['from_city']) ?> ➔ <?= htmlspecialchars($f['to_city']) ?></td>
                    <td>⏱ <?= date('H:i', strtotime($f['departure_time'])) ?> - <?= date('H:i', strtotime($f['arrival_time'])) ?> (<?= htmlspecialchars($f['duration']) ?>)</td>
                    <td><strong><?= number_format($f['price']) ?> BDT</strong></td>
                    <td><a href="admin.php?delete_flight_id=<?= $f['id'] ?>" class="btn-red" onclick="return confirm('Are you sure you want to completely delete this flight plan from the system?');">Delete Flight</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>🎟️ Passenger Bookings & Roster</h3>
    <table>
        <thead>
            <tr>
                <th>PNR</th>
                <th>Passenger Name</th>
                <th>Flight Route</th>
                <th>Class</th>
                <th>Total Paid</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr><td colspan="6" class="no-data">No flight tickets booked by passengers yet.</td></tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): 
                    $flight_from = $b['f_from'] ?? 'N/A';
                    $flight_to = $b['f_to'] ?? 'N/A';
                    $airline = $b['f_airline'] ?? 'Domestic';
                    $passenger = $b['b_passenger'] ?? 'Passenger';
                    $seat_class = $b['b_class'] ?? 'Economy Class';
                    $total_price = (!empty($b['b_price']) && $b['b_price'] > 0) ? $b['b_price'] : ($b['f_price'] ?? '0');
                ?>
                <tr>
                    <td><span class="pnr-badge"><?= htmlspecialchars($b['b_pnr']) ?></span></td>
                    <td><?= htmlspecialchars($passenger) ?></td>
                    <td><?= htmlspecialchars($flight_from) ?> ➔ <?= htmlspecialchars($flight_to) ?><br><small style="color:#64748b">✈ <?= htmlspecialchars($airline) ?></small></td>
                    <td><span class="class-badge"><?= htmlspecialchars($seat_class) ?></span></td>
                    <td><strong><?= number_format($total_price) ?> BDT</strong></td>
                    <td><a href="admin.php?delete_booking_id=<?= $b['b_id'] ?>" class="btn-red" onclick="return confirm('Cancel this passenger booking?');">Cancel Ticket</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>👥 Registered System Users</h3>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>System Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><b>#<?= $u['id'] ?></b></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><code><?= htmlspecialchars($u['email']) ?></code></td>
                <td><span class="role-badge"><?= htmlspecialchars($u['role']) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn-back">⬅ Back to Main Site</a>
</div>
</body>
</html>