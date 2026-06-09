<?php
require 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $password]);
        $message = "<div class='alert success'>🎉 Registration Successful! <a href='login.php'>Login here</a></div>";
    } catch (Exception $e) {
        $message = "<div class='alert error'>❌ Email already exists or error occurred!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - VromonVibe</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; animation: fadeInPage 0.6s ease-in-out; }
        @keyframes fadeInPage { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); width: 380px; box-sizing: border-box; text-align: center; }
        h2 { color: #1A73E8; margin: 0; font-size: 28px; font-weight: 700; font-style: italic; }
        input { width: 100%; padding: 12px 15px; margin: 12px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Poppins', sans-serif; font-size: 14px; outline: none; transition: 0.3s; }
        input:focus { border-color: #1A73E8; box-shadow: 0 0 8px rgba(26,115,232,0.15); }
        
        .btn-submit { width: 100%; padding: 14px; background-color: #1A73E8; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; margin-top: 15px; cursor: pointer; transition: 0.3s ease; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(26,115,232,0.2); }
        .btn-submit:hover { background-color: #1557b0; transform: scale(1.02); }
        
        /* স্পিনার অ্যানিমেশন সিএসএস */
        .spinner { width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 1s ease infinite; display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
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
        <h2>VromonVibe ✈</h2>
        <p style="color: #666; margin-top: 5px; margin-bottom: 25px; font-size: 14px; font-weight: 500;">Create your travel account</p>
        <?php echo $message; ?>
        <form method="POST" onsubmit="showLoading(this)">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-submit" id="submitBtn">
                <div class="spinner" id="btnSpinner"></div>
                <span id="btnText">Sign Up</span>
            </button>
        </form>
        <div class="link">Already have an account? <a href="login.php">Login</a></div>
    </div>

    <script>
        function showLoading(form) {
            document.getElementById('btnSpinner').style.display = 'block';
            document.getElementById('btnText').innerText = 'Processing Safely...';
            document.getElementById('submitBtn').style.pointerEvents = 'none';
            document.getElementById('submitBtn').style.opacity = '0.8';
        }
    </script>
</body>
</html>