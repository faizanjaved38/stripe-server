<?php
require_once '../vendor/autoload.php';
require_once '../secrets.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

\Stripe\Stripe::setApiKey($stripeSecretKey);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
 if (isset($_GET['customer_id'])) {
    $customerId = $_GET['customer_id'];
    try {
        $subscriptions = \Stripe\Subscription::all([
            'customer' => $customerId,
            'status' => 'active',
        ]);

        echo json_encode($subscriptions);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}else {
    echo json_encode(['error' => 'customer_id not provided']);
}
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
