<?php
$host = 'localhost';  // Change to your database host
$dbname = 'dbmycweeklhrog';  // Your database name
$username = 'uygoosds9dynj';  // Your database username
$password = 'hof3udlwhgyd';  // Your database password
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
