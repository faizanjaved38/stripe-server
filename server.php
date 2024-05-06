<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST"); // Specify the allowed HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Specify the allowed headers

// Require the Stripe PHP library
require_once 'vendor/autoload.php';

// Set your secret key
\Stripe\Stripe::setApiKey('sk_test_51P9PdRGSVAelLEOgaiLoLQDoR7z89w5jW0UKT7iuciOyiWSNpq7P9I8AL2wTNrd3mqRyuRvzAPU1SjUyhyBX7OpS00rHGZoFqX');

// Database connection details
$servername = "localhost";
$username = "phpmyadmin";
$password = "faizan";
$database = "stripe";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo ("Connection Runnnig");
// Create a customer
function createCustomer($email, $name) {
    return \Stripe\Customer::create([
        'email' => $email,
        'name' => $name
    ]);
}

// Create a subscription for a customer
function createSubscription($customerId) {
    // return \Stripe\Subscription::create([
    //     'customer' => $customerId,
    //     // 'items' => [['plan' => $planId]],
    // ]);
}

// Handle POST request to create a customer and subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    // Create a customer
    $customer = createCustomer($request->email, $request->name);

    if ($customer) {
        // Create a subscription for the customer
        // $subscription = createSubscription($customer->id);

        // if ($subscription) {
            // Store customer and subscription information in your database
            // $sql = "INSERT INTO subscriptions (customer_id, subscription_id) VALUES ('$customer->id', '$subscription->id')";
            $sql = "INSERT INTO subscriptions (url, customer_id, subscription_id) VALUES ('123','$customer->id', '1234')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error storing subscription information: ' . $conn->error]);
            }
        // } else {
        //     echo json_encode(['success' => false, 'error' => 'Error creating subscription']);
        // }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error creating customer']);
    }
}

// Close the database connection
$conn->close();
?>
