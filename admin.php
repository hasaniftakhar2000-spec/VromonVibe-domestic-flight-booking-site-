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
    
    // ডাটাবেস থেকে নির্দিষ্ট টিকিটটি ডিলিট করা
    $delete_stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $delete_stmt->execute([$booking_id]);
    
    // ডিলিট হওয়ার পর সাকসেস মেসেজসহ পেজ রিফ্রেশ করা
    header("Location: admin.php?status=success");
    exit;
}

// ডাটাবেসের সব বুকিং বা টিকিট হিস্ট্রি তুলে আনা
$stmt = $pdo->query("SELECT * FROM bookings ORDER BY id DESC");
$all_bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Passenger Ticket Roster</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f9; margin: 0; padding: 30px; }
        .admin-container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { color: #1e3a8a; margin-top: 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        
        /* সাকসেস অ্যালার্ট */
        .alert-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; }
        
        /* টেবিল স্টাইল */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #1A73E8; color: white; font-weight: 600; }
        tr:hover { background-color: #f8fafc; }
        
        .pnr-badge { font-weight: bold; color: #2563eb; background: #dbeafe; padding: 4px 8px; border-radius: 4px; font-family: monospace; }
        .btn-delete { background: #ef4444; color: white; padding: 8px 14px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: 0.2s ease; border: none; cursor: pointer; }
        .btn-delete:hover { background: #dc2626; transform: scale(1.02); }
        .btn-back { background: #4b5563; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 20px; font-weight: bold; }
        .btn-back:hover { background: #374151; }
        .no-data { text-align: center; color: #9ca3af; padding: 30px 0; font-style: italic; }
    </style>
</head>
<body>

<div class="admin-container">
    <h2>Admin Dashboard ✈️ - Passenger Ticket Roster</h2>
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert-success">✓ Ticket has been successfully cancelled and removed from the roster!</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Passenger Name</th>
                <th>Route</th>
                <th>Class</th>
                <th>Total Paid</th>
                <th>PNR Code</th>
                <th>Actions</th> </tr>
        </thead>
        <tbody>
            <?php if (empty($all_bookings)): ?>
                <tr>
                    <td colspan="7" class="no-data">No flight tickets booked by passengers yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($all_bookings as $booking): 
                    // ডাইনামিক কলাম অটো-ডিটেকশন (dashboard.php এর মতোই সুরক্ষিত ব্যবস্থা)
                    $flight_from = $booking['flight_from'] ?? $booking['from_city'] ?? $booking['source'] ?? 'N/A';
                    $flight_to = $booking['flight_to'] ?? $booking['to_city'] ?? $booking['destination'] ?? 'N/A';
                    $passenger = $booking['passenger_name'] ?? $booking['name'] ?? 'Passenger';
                    $seat_class = $booking['seat_class'] ?? $booking['class'] ?? 'Economy';
                    $total_price = $booking['total_price'] ?? $booking['price'] ?? '0';
                    $pnr = $booking['pnr'] ?? $booking['ticket_number'] ?? 'N/A';
                    $b_id = $booking['id'] ?? $booking['booking_id'] ?? 0;
                ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($b_id); ?></strong></td>
                        <td><?php echo htmlspecialchars($passenger); ?></td>
                        <td><?php echo htmlspecialchars($flight_from); ?> ➔ <?php echo htmlspecialchars($flight_to); ?></td>
                        <td><?php echo htmlspecialchars($seat_class); ?></td>
                        <td><strong><?php echo is_numeric($total_price) ? number_format((float)$total_price) : htmlspecialchars($total_price); ?> BDT</strong></td>
                        <td><span class="pnr-badge"><?php echo htmlspecialchars($pnr); ?></span></td>
                        <td>
                            <a href="admin.php?delete_booking_id=<?php echo $b_id; ?>" 
                               onclick="return confirm('Are you sure you want to permanently cancel and delete this passenger ticket?');" 
                               class="btn-delete">
                               Delete Ticket
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn-back">⬅ Back to Main Site</a>
</div>

</body>
</html>