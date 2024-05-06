<?php
require_once "./db_conn.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST"); // Specify the allowed HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Specify the allowed headers

// Set your secret key
\Stripe\Stripe::setApiKey('sk_test_51P9PdRGSVAelLEOgaiLoLQDoR7z89w5jW0UKT7iuciOyiWSNpq7P9I8AL2wTNrd3mqRyuRvzAPU1SjUyhyBX7OpS00rHGZoFqX');

// Handle POST request to create a customer and subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $url = $request->url; // Assuming url is sent from frontend

    // Prepare and bind the statement
    $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE hostname = ?");
    $stmt->bind_param("s", $url);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists
        echo json_encode(['exists' => true]);
    } else {
        // User does not exist
        echo json_encode(['exists' => false]);
    }

}

// Close the database connection
$conn->close();
?>
