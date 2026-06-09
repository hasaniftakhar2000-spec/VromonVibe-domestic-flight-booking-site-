<?php
session_start();
require 'db.php';

$from = isset($_GET['from_city']) ? $_GET['from_city'] : '';
$to = isset($_GET['to_city']) ? $_GET['to_city'] : '';
$date = isset($_GET['travel_date']) ? $_GET['travel_date'] : date('Y-m-d');

if (!empty($from) && !empty($to)) {
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE from_city = ? AND to_city = ?");
    $stmt->execute([$from, $to]);
} else {
    $stmt = $pdo->query("SELECT * FROM flights LIMIT 4");
}
$flights = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VromonVibe - Domestic Flight Booking</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body { 
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; 
            margin: 0; 
            background-color: #f0f4f9; 
            color: #333; 
            /* ১. পেজ লোড হওয়ার সুন্দর অ্যানিমেশন */
            animation: fadeInPage 0.6s ease-in-out;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .navbar { background: white; padding: 15px 80px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .logo { font-size: 26px; font-weight: bold; color: #1A73E8; text-decoration: none; font-style: italic; }
        .nav-links { display: flex; align-items: center; }
        .nav-links span { margin-right: 15px; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #444; font-weight: 500; }
        
        .btn-login { background: #1A73E8; color: white !important; padding: 10px 22px; border-radius: 25px; transition: 0.3s ease; margin-left: 20px; }
        .btn-login:hover { background: #1557b0; transform: scale(1.05); box-shadow: 0 4px 10px rgba(26,115,232,0.2); }
        
        /* ড্যাশবোর্ড বাটনের প্রিমিয়াম স্টাইল */
        .btn-dashboard { background: #E8F0FE; color: #1A73E8 !important; padding: 8px 18px; border-radius: 20px; font-weight: 600; border: 1px solid #1A73E8; transition: 0.3s ease; }
        .btn-dashboard:hover { background: #1A73E8; color: white !important; transform: scale(1.03); }

        /* Premium Hero & Search Box Section */
        .hero { background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.2)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1353&q=80') no-repeat center center/cover; height: 420px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; }
        .hero h1 { font-size: 38px; margin-bottom: 20px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); font-weight: 700; }
        
        .search-container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); width: 850px; color: #333; margin-top: -60px; z-index: 10; position: relative; }
        .search-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .search-field { display: flex; flex-direction: column; border: 1px solid #ddd; padding: 10px 15px; border-radius: 8px; background: #fff; }
        .search-field label { font-size: 11px; text-transform: uppercase; color: #777; font-weight: bold; margin-bottom: 5px; }
        .search-field select, .search-field input { border: none; outline: none; font-size: 16px; font-weight: 600; color: #222; background: transparent; font-family: 'Poppins', sans-serif; }
        
        .btn-search { background: #FFC107; color: #000; border: none; width: 100%; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s ease; }
        
        /* ২. সার্চ বাটনে হোভার অ্যানিমেশন */
        .btn-search:hover { 
            background: #e0a800; 
            transform: scale(1.02); 
            box-shadow: 0 6px 20px rgba(255,193,7,0.4); 
        }

        /* Flight Cards Section */
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        .flight-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; border-left: 5px solid #1A73E8; transition: 0.3s ease; }
        .flight-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        
        .airline-info { font-size: 18px; font-weight: bold; color: #222; }
        .airline-sub { font-size: 12px; color: #FF9800; font-weight: bold; }
        
        .time-block { text-align: center; }
        .time-block h2 { margin: 0; font-size: 22px; color: #111; font-weight: 600; }
        .time-block p { margin: 5px 0 0 0; color: #777; font-size: 14px; font-weight: bold; }
        
        .duration-line { text-align: center; color: #999; font-size: 12px; position: relative; width: 140px; }
        .duration-line::after { content: ''; display: block; width: 100%; height: 2px; background: #ccc; margin-top: 8px; }
        
        /* ৩. চলন্ত উড়োজাহাজের (Moving Plane) চোখ ধাঁধানো অ্যানিমেশন */
        .duration-line::before {
            content: '✈';
            position: absolute;
            top: -2px;
            left: 0;
            color: #1A73E8;
            font-size: 14px;
            animation: flyPlane 4s linear infinite;
        }

        @keyframes flyPlane {
            0% { left: 0%; transform: scaleX(1); }
            50% { left: 85%; transform: scaleX(1); }
            51% { transform: scaleX(-1); } 
            99% { left: 0%; transform: scaleX(-1); }
            100% { transform: scaleX(1); }
        }

        .price-block { text-align: right; }
        .price { font-size: 24px; font-weight: bold; color: #1A73E8; margin-bottom: 10px; }
        
        .btn-book { background: #FFC107; color: black; border: none; padding: 10px 22px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 14px; display: inline-block; transition: 0.3s ease; }
        
        /* ৪. বুকিং বাটনে হোভার অ্যানিমেশন */
        .btn-book:hover { 
            background: #e0a800; 
            transform: scale(1.05); 
            box-shadow: 0 4px 15px rgba(255,193,7,0.4); 
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">VromonVibe ✈</a>
        <div class="nav-links">
            <?php if(isset($_SESSION['user_name'])): ?>
                <span>Welcome, <strong><?php echo ucwords(strtolower($_SESSION['user_name'])); ?></strong></span>
                
                <a href="dashboard.php" class="btn-dashboard">My Dashboard</a>
                
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                    <a href="admin.php" style="color: red; font-weight: bold;">Admin Panel</a>
                <?php endif; ?>
                
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Sign In</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero">
        <h1>Flight বুকিং হোক সহজে</h1>
    </div>

    <div class="container">
        <div class="search-container">
            <form method="GET" action="index.php">
                <div class="search-grid">
                    <div class="search-field">
                        <label>City From</label>
                        <select name="from_city" required>
                            <option value="Dhaka" <?php if($from=='Dhaka') echo 'selected'; ?>>Dhaka (DAC)</option>
                            <option value="Saidpur" <?php if($from=='Saidpur') echo 'selected'; ?>>Saidpur (SPD)</option>
                            <option value="Khulna" <?php if($from=='Khulna') echo 'selected'; ?>>Khulna (KHU)</option>
                            <option value="Sylhet" <?php if($from=='Sylhet') echo 'selected'; ?>>Sylhet (ZYL)</option>
                            <option value="Cox's Bazar" <?php if($from=="Cox's Bazar") echo 'selected'; ?>>Cox's Bazar (CXB)</option>
                        </select>
                    </div>
                    <div class="search-field">
                        <label>City To</label>
                        <select name="to_city" required>
                            <option value="Saidpur" <?php if($to=='Saidpur') echo 'selected'; ?>>Saidpur (SPD)</option>
                            <option value="Dhaka" <?php if($to=='Dhaka') echo 'selected'; ?>>Dhaka (DAC)</option>
                            <option value="Khulna" <?php if($to=='Khulna') echo 'selected'; ?>>Khulna (KHU)</option>
                            <option value="Sylhet" <?php if($to=='Sylhet') echo 'selected'; ?>>Sylhet (ZYL)</option>
                            <option value="Cox's Bazar" <?php if($to=="Cox's Bazar") echo 'selected'; ?>>Cox's Bazar (CXB)</option>
                        </select>
                    </div>
                    <div class="search-field">
                        <label>Journey Date</label>
                        <input type="date" name="travel_date" value="<?php echo htmlspecialchars($date); ?>" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <button type="submit" class="btn-search">Search Flights</button>
            </form>
        </div>

        <h2 style="margin-top: 40px; color: #222;">Available Flights</h2>
        <?php if(count($flights) == 0): ?>
            <p style="color: #666; background: white; padding: 20px; border-radius: 8px;">দুঃখিত! এই রুটে কোনো ফ্লাইট পাওয়া যায়নি।</p>
        <?php endif; ?>

        <?php foreach($flights as $flight): ?>
            <div class="flight-card">
                <div>
                    <div class="airline-info">✈ <?php echo htmlspecialchars($flight['airline']); ?></div>
                    <div class="airline-sub"><?php echo htmlspecialchars($flight['flight_number']); ?> • Economy</div>
                </div>
                <div class="time-block">
                    <h2><?php echo date('H:i', strtotime($flight['departure_time'])); ?></h2>
                    <p><?php echo htmlspecialchars($flight['from_city']); ?></p>
                </div>
                <div class="duration-line">
                    <?php echo htmlspecialchars($flight['duration']); ?><br>Non Stop
                </div>
                <div class="time-block">
                    <h2><?php echo date('H:i', strtotime($flight['arrival_time'])); ?></h2>
                    <p><?php echo htmlspecialchars($flight['to_city']); ?></p>
                </div>
                <div class="price-block">
                    <div class="price">BDT <?php echo number_format($flight['price']); ?></div>
                    <a href="booking.php?flight_id=<?php echo $flight['id']; ?>&date=<?php echo $date; ?>" class="btn-book">Book Flight</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>