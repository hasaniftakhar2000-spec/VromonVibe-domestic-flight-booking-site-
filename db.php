<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'sql300.infinityfree.com';
$db   = 'if0_42135094_vromonvibe'; 
$user = 'if0_42135094';
$pass = 'nXYTFKUcOwQNY95'; // আপনার আসল পাসওয়ার্ডটি এখানে বসিয়ে দেওয়া হয়েছে
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     echo "<div style='background:#ffebee; color:#c62828; padding:20px; border-radius:8px; font-family:sans-serif;'>";
     echo "<h3>Database Connection Error:</h3>" . $e->getMessage();
     echo "</div>";
     exit;
}
?>