<?php
// Include the Stripe PHP library
require_once 'vendor/autoload.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST"); // Specify the allowed HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Specify the allowed headers

// Set your Stripe API secret key
\Stripe\Stripe::setApiKey('sk_test_51P9PdRGSVAelLEOgaiLoLQDoR7z89w5jW0UKT7iuciOyiWSNpq7P9I8AL2wTNrd3mqRyuRvzAPU1SjUyhyBX7OpS00rHGZoFqX');

// Define the endpoint handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the domain URL from the request headers
    $domainURL = $_SERVER['HTTP_REFERER'];
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    // Get the price ID from the request body
    // $priceId = $_POST['priceId'];
    $priceId = $request->priceId;

    $YOUR_DOMAIN = 'http://localhost:4200';
    // Create new Checkout Session for the order
    $session = \Stripe\Checkout\Session::create([
        // 'mode' => 'subscription',
        // 'payment_method_types' => ['card'],
        // 'billing_address_collection' => 'auto',
        // 'shipping_address_collection' => [
        //     'allowed_countries' => ['US', 'CA'],
        // ],
        // 'line_items' => [
        //     [
        //         'price' => $priceId,
        //         'quantity' => 1,
        //     ],
        // ],
        // 'success_url' => $domainURL . '/success.html?session_id={CHECKOUT_SESSION_ID}',
        // 'cancel_url' => $domainURL . '/canceled.html',
        'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price' => $priceId,
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '?success=true',
          'cancel_url' => $YOUR_DOMAIN . '?canceled=true',
    ]);

    // Send the session ID as the response
    echo json_encode(['sessionId' => $session->id]);
    echo json_encode(['$domainURL' => $domainURL]);
}
?>
