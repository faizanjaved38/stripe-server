<?php
require_once '../vendor/autoload.php';
require_once '../secrets.php';
require_once "../db_conn.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set your secret key
\Stripe\Stripe::setApiKey($stripeSecretKey);
// Handle GET request to check the hostname
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['hostname'])) {
        $hostname = $_GET['hostname'];

        // Prepare and bind the statement
        $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE hostname = ?");
        $stmt->bind_param("s", $hostname);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['exists' => true, 'customer_id' => $row['customer_id']]);
        } else {
            echo json_encode(['exists' => false]);
        }
    } else {
        echo json_encode(['error' => 'Hostname not provided']);
    }
}

// Close the database connection
$conn->close();
?>
