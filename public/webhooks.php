<?php
// webhook.php
//
// Use this sample code to handle webhook events in your integration.
//
// 1) Paste this code into a new file (webhook.php)
//
// 2) Install dependencies
//   composer require stripe/stripe-php
//
// 3) Run the server on http://localhost:4242
//   php -S localhost:4242

require_once '../vendor/autoload.php';
require_once '../secrets.php';
require_once "../db_conn.php";
// \Stripe\Stripe::setApiKey($stripeSecretKey);

// The library needs to be configured with your account's secret key.
// Ensure the key is kept out of any version control system you might be using.
$stripe = new \Stripe\StripeClient($stripeSecretKey);

// This is your Stripe CLI webhook secret for testing your endpoint locally.
$endpoint_secret = 'whsec_5e2b532fa754b2e43f3a6bdf5074a002daab964378e8b55258811c2d22475c1a';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

// Handle the event
switch ($event->type) {
  case 'checkout.session.completed':
    $checkout = $event->data->object;
    // Verify the payment status and subscription state
    if ($checkout->payment_status === 'paid' && isset($checkout->subscription)) {
        $hostname = $checkout->metadata->hostname;
        $customer_id = $checkout->customer;
        $subscription_id = $checkout->subscription;
        $user_email = $checkout->customer_details->email;
        $user_name = $checkout->customer_details->name;
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO subscriptions (hostname, customer_id, subscription_id ,user_email ,user_name) VALUES (?, ?, ? ,? ,?)");
        $stmt->bind_param("sssss", $hostname, $customer_id, $subscription_id, $user_email, $user_name);
        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error storing subscription information: ' . $conn->error]);
        }
        $stmt->close();
    }
  break;
  
  case 'customer.subscription.deleted':
    $subscription = $event->data->object;
    $customer_id = $subscription->customer;
    $subscription_id = $subscription->id;
    
    // Prepare the statement to delete the subscription entry
    $stmt = $conn->prepare("DELETE FROM subscriptions WHERE customer_id = ? OR subscription_id = ?");
    $stmt->bind_param("ss", $customer_id, $subscription_id);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error deleting subscription information: ' . $conn->error]);
    }
    $stmt->close();
  break;
  default:
    echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);