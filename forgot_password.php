<?php
require 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ইমেইলটি ডেটাবেসে আছে কিনা চেক করা হচ্ছে
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($new_password === $confirm_password) {
            // ওটিপি ছাড়া সরাসরি নতুন পাসওয়ার্ড সিকিউরলি হ্যাশ করে আপডেট করা হচ্ছে
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            $update_stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
            $update_stmt->execute([$hashed_password, $email]);
            
            $message = "<div class='alert success'>🎉 Password Reset Successful! <a href='login.php'>Login now</a></div>";
        } else {
            $message = "<div class='alert error'>❌ New Passwords do not match!</div>";
        }
    } else {
        $message = "<div class='alert error'>❌ Email address not found in our system!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - VromonVibe</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; animation: fadeInPage 0.6s ease-in-out; }
        @keyframes fadeInPage { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); width: 380px; box-sizing: border-box; text-align: center; }
        h2 { color: #1A73E8; margin: 0; font-size: 26px; font-weight: 700; font-style: italic; }
        input { width: 100%; padding: 12px 15px; margin: 12px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Poppins', sans-serif; font-size: 14px; outline: none; transition: 0.3s; }
        input:focus { border-color: #1A73E8; box-shadow: 0 0 8px rgba(26,115,232,0.15); }
        
        .password-container { position: relative; width: 100%; }
        .eye-icon { position: absolute; right: 15px; top: 22px; cursor: pointer; color: #666; user-select: none; font-size: 16px; }
        
        .btn-submit { width: 100%; padding: 14px; background-color: #FFC107; color: black; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; margin-top: 15px; cursor: pointer; transition: 0.3s ease; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(255,193,7,0.2); }
        .btn-submit:hover { background-color: #e0a800; transform: scale(1.02); }
        
        .link { margin-top: 20px; font-size: 14px; color: #555; }
        .link a { color: #1A73E8; text-decoration: none; font-weight: 600; }
        .alert { padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
        .success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .success a { color: #1b5e20; font-weight: bold; }
        .error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Reset Password ✈</h2>
        <p style="color: #666; margin-top: 5px; margin-bottom: 25px; font-size: 14px; font-weight: 500;">Direct Security Password Update</p>
        <?php echo $message; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Your Registered Email" required>
            
            <div class="password-container">
                <input type="password" id="new_pass" name="new_password" placeholder="New Password" required>
                <span class="eye-icon" onclick="togglePassword('new_pass', this)">👁️</span>
            </div>
            
            <div class="password-container">
                <input type="password" id="confirm_pass" name="confirm_password" placeholder="Confirm New Password" required>
                <span class="eye-icon" onclick="togglePassword('confirm_pass', this)">👁️</span>
            </div>
            
            <button type="submit" class="btn-submit">Update Password & Done</button>
        </form>
        <div class="link"><a href="login.php">⬅ Back to Login</a></div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.style.color = "#1A73E8";
            } else {
                input.type = "password";
                icon.style.color = "#666";
            }
        }
    </script>
</body>
</html>