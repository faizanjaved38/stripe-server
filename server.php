<?php
require_once "./db_conn.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST"); // Specify the allowed HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Specify the allowed headers

// Set your secret key
\Stripe\Stripe::setApiKey('sk_test_51P9PdRGSVAelLEOgaiLoLQDoR7z89w5jW0UKT7iuciOyiWSNpq7P9I8AL2wTNrd3mqRyuRvzAPU1SjUyhyBX7OpS00rHGZoFqX');

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
            // Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO subscriptions (hostname, customer_id, subscription_id) VALUES (?, ?, ?)");
$subscription_id = '1234'; // Define the value as a variable
$stmt->bind_param("sss", $request->hostname, $customer->id, $subscription_id);
// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error storing subscription information: ' . $conn->error]);
}

// Close the statement
$stmt->close();
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
