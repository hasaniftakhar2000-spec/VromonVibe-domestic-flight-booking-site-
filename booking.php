<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$flight_id = isset($_GET['flight_id']) ? intval($_GET['flight_id']) : 1;
$travel_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// ফ্লাইটের তথ্য আনা
$stmt = $pdo->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->execute([$flight_id]);
$flight = $stmt->fetch();

if (!$flight) {
    echo "Flight not found.";
    exit;
}

$show_ticket = false;
$ticket_number = '';
$passenger_name = '';
$ticket_class = 'Economy Class';
$final_price = $flight['price'];
$payment_method = 'bKash';

// 🛠️ এখানে ব্র্যাকেটের ভুলটি নিখুঁতভাবে ফিক্স করা হয়েছে
if (isset($_POST['confirm_payment'])) {
    $passenger_name = $_POST['passenger_name'];
    $ticket_class = $_POST['ticket_class'];
    $payment_method = $_POST['payment_method'];
    $final_price = $_POST['final_price'];
    $ticket_number = 'VV-' . strtoupper(uniqid()); // ইউনিক টিকিট PNR জেনারেশন

    // ডাটাবেসের অরিজিনাল কলাম অনুযায়ী কুয়েরি
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, flight_id, passenger_name, booking_date, ticket_number, payment_status) VALUES (?, ?, ?, ?, ?, 'Paid')");
    $stmt->execute([$_SESSION['user_id'], $flight_id, $passenger_name, $travel_date, $ticket_number]);
    
    $show_ticket = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flight Booking & Ticket - VromonVibe</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body { 
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background-color: #f0f4f9; 
            color: #333; 
            /* ১. পেজ লোড হওয়ার সুন্দর অ্যানিমেশন */
            animation: fadeInPage 0.6s ease-in-out;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .box { 
            max-width: 600px; 
            margin: 30px auto; 
            background: white; 
            padding: 35px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.06); 
        }

        h2 { color: #1A73E8; margin-top: 0; font-weight: 700; font-size: 24px; }
        h3 { font-size: 16px; color: #444; margin-top: 25px; }
        
        .flight-summary { 
            background: #e8f0fe; 
            padding: 18px; 
            border-radius: 10px; 
            font-weight: 600; 
            line-height: 1.7; 
            color: #1A73E8;
            border-left: 5px solid #1A73E8;
            font-size: 15px;
        }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #555; }
        .input-group input, .input-group select { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px; font-family: 'Poppins', sans-serif; outline: none; transition: 0.3s; }
        .input-group input:focus, .input-group select:focus { border-color: #1A73E8; box-shadow: 0 0 8px rgba(26,115,232,0.15); }
        
        /* পেমেন্ট কার্ড স্টাইল */
        .payment-methods { display: flex; gap: 15px; margin: 15px 0 25px 0; }
        .method { 
            border: 2px solid #eee; 
            padding: 14px; 
            border-radius: 10px; 
            cursor: pointer; 
            flex: 1; 
            text-align: center; 
            font-weight: 600; 
            transition: 0.3s ease; 
            background: #fafafa;
            color: #555;
            font-size: 14px;
        }
        .method:hover { border-color: #1A73E8; transform: translateY(-2px); }
        
        /* একটিভ পেমেন্ট কার্ডের চমৎকার গ্লো ইফ效ক্ট */
        .method.active { 
            border-color: #1A73E8; 
            color: #1A73E8; 
            background: #e8f0fe; 
            box-shadow: 0 6px 15px rgba(26,115,232,0.15); 
            transform: translateY(-2px);
        }

        .btn-pay { 
            background: #22C55E; 
            color: white; 
            border: none; 
            width: 100%; 
            padding: 15px; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s ease; 
            box-shadow: 0 4px 12px rgba(34,197,94,0.2);
        }
        .btn-pay:hover { background: #16a34a; transform: scale(1.02); box-shadow: 0 6px 20px rgba(34,197,94,0.3); }
        
        /* Premium E-Ticket Design */
        .ticket { border: 2px dashed #1A73E8; padding: 30px; border-radius: 14px; background: #fff; position: relative; }
        .ticket::before, .ticket::after { content: ''; position: absolute; width: 20px; height: 20px; background: #f0f4f9; border-radius: 50%; top: 75px; }
        .ticket::before { left: -12px; }
        .ticket::after { right: -12px; }
        
        .ticket-header { display: flex; justify-content: space-between; border-bottom: 2px solid #f1f3f4; padding-bottom: 18px; margin-bottom: 20px; align-items: center; }
        .ticket-logo { font-size: 24px; font-weight: bold; color: #1A73E8; font-style: italic; }
        .ticket-id { font-weight: 700; color: #E11D48; font-size: 16px; background: #ffe4e6; padding: 4px 12px; border-radius: 20px; }
        
        .ticket-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 10px; }
        .ticket-field { display: flex; flex-direction: column; }
        .ticket-field label { font-size: 11px; color: #888; display: block; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .ticket-field span { font-size: 15px; font-weight: 600; color: #222; }
        
        .btn-print { background: #1A73E8; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; font-weight: bold; display: block; margin: 25px auto 0 auto; transition: 0.3s ease; box-shadow: 0 4px 12px rgba(26,115,232,0.2); }
        .btn-print:hover { background: #1557b0; transform: scale(1.05); box-shadow: 0 6px 18px rgba(26,115,232,0.3); }
        
        @media print { 
            body { background: white; padding: 0; }
            .box { box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            body * { visibility: hidden; } 
            .ticket, .ticket * { visibility: visible; } 
            .ticket { position: absolute; left: 0; top: 0; width: 100%; border: 2px solid #333; } 
            .btn-print, .btn-home-link { display: none !important; } 
        }
    </style>
</head>
<body>

<?php if (!$show_ticket): ?>
    <div class="box">
        <h2>Confirm Your Flight ✈</h2>
        <div class="flight-summary">
            <strong>Airline:</strong> <?php echo htmlspecialchars($flight['airline']); ?> (<?php echo htmlspecialchars($flight['flight_number']); ?>)<br>
            <strong>Route:</strong> <?php echo htmlspecialchars($flight['from_city']); ?> ➔ <?php echo htmlspecialchars($flight['to_city']); ?> <br>
            <strong>Schedule:</strong> <?php echo htmlspecialchars($travel_date); ?> | ⏱ <?php echo date('H:i', strtotime($flight['departure_time'])); ?>
        </div>
        
        <form method="POST" id="bookingForm" style="margin-top: 25px;">
            <div class="input-group">
                <label>Passenger Name</label>
                <input type="text" name="passenger_name" value="<?php echo ucwords(strtolower($_SESSION['user_name'])); ?>" required>
            </div>

            <div class="input-group">
                <label>Select Cabin Class</label>
                <select name="ticket_class" id="ticket_class" onchange="calculateTotal()">
                    <option value="Economy Class" data-addon="0">Economy Class (Base Fare)</option>
                    <option value="Business Class" data-addon="3000">Business Class (+BDT 3,000 Premium)</option>
                </select>
            </div>
            
            <h3>Select Payment Method (Demo)</h3>
            <input type="hidden" name="payment_method" id="selected_payment" value="bKash">
            <div class="payment-methods">
                <div class="method active" onclick="changePayment('bKash', this)">bKash</div>
                <div class="method" onclick="changePayment('Nagad', this)">Nagad</div>
                <div class="method" onclick="changePayment('Visa/MasterCard', this)">Visa/MasterCard</div>
            </div>

            <div class="input-group">
                <label>Total Payable Fare</label>
                <input type="hidden" name="final_price" id="final_price" value="<?php echo $flight['price']; ?>">
                <input type="text" id="total_fare_display" value="BDT <?php echo number_format($flight['price']); ?>" disabled style="background: #f1f3f4; font-weight: bold; color: #1A73E8; font-size: 18px; border: none;">
            </div>

            <button type="submit" name="confirm_payment" class="btn-pay">Pay & Generate Secure Ticket</button>
        </form>
    </div>

    <script>
        const basePrice = <?php echo $flight['price']; ?>;

        function calculateTotal() {
            const classSelect = document.getElementById('ticket_class');
            const addon = parseInt(classSelect.options[classSelect.selectedIndex].getAttribute('data-addon'));
            const total = basePrice + addon;
            
            document.getElementById('total_fare_display').value = "BDT " + total.toLocaleString();
            document.getElementById('final_price').value = total;
        }

        function changePayment(methodName, element) {
            document.querySelectorAll('.method').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('selected_payment').value = methodName;
        }
    </script>

<?php else: ?>
    <div class="box" style="max-width: 650px;">
        <div class="ticket">
            <div class="ticket-header">
                <div class="ticket-logo">VromonVibe ✈</div>
                <div class="ticket-id">PNR: <?php echo $ticket_number; ?></div>
            </div>
            <div class="ticket-grid">
                <div class="ticket-field"><label>Passenger Name</label><span><?php echo htmlspecialchars($passenger_name); ?></span></div>
                <div class="ticket-field"><label>Payment Mode</label><span><?php echo htmlspecialchars($payment_method); ?> (Paid)</span></div>
                <div class="ticket-field"><label>Flight Number</label><span><?php echo htmlspecialchars($flight['flight_number']); ?> (<?php echo htmlspecialchars($flight['airline']); ?>)</span></div>
                <div class="ticket-field"><label>Cabin Class</label><span style="color: #1A73E8;"><?php echo htmlspecialchars($ticket_class); ?></span></div>
                <div class="ticket-field"><label>From City</label><span><?php echo htmlspecialchars($flight['from_city']); ?></span></div>
                <div class="ticket-field"><label>To City</label><span><?php echo htmlspecialchars($flight['to_city']); ?></span></div>
                <div class="ticket-field"><label>Departure</label><span><?php echo date('H:i', strtotime($flight['departure_time'])); ?></span></div>
                <div class="ticket-field"><label>Arrival</label><span><?php echo date('H:i', strtotime($flight['arrival_time'])); ?></span></div>
                <div class="ticket-field"><label>Journey Date</label><span><?php echo htmlspecialchars($travel_date); ?></span></div>
                <div class="ticket-field"><label>Total Amount</label><span>BDT <?php echo number_format($final_price); ?></span></div>
            </div>
            <div style="text-align: center; font-size: 11px; color: #aaa; margin-top: 25px; border-top: 1px dashed #ccc; padding-top: 12px;">
                Thank you for choosing VromonVibe. Please bring a printed PDF copy of this digital ticket to the airport counter.
            </div>
        </div>
        <button onclick="window.print()" class="btn-print">Print E-Ticket to PDF 🖨</button>
        <p class="btn-home-link" style="text-align: center; margin-top: 20px; font-size: 14px;"><a href="index.php" style="color: #1A73E8; text-decoration: none; font-weight: bold;">← Return to Home</a></p>
    </div>
<?php endif; ?>

</body>
</html>