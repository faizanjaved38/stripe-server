<?php
// Require the Stripe PHP library
require_once 'vendor/autoload.php';
// Database connection details
$servername = "localhost";
$username = "faizan";
$password = "faizan1234";
$database = "stripe";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

