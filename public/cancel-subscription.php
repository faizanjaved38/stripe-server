<?php
require_once '../vendor/autoload.php';
require_once '../secrets.php';
\Stripe\Stripe::setApiKey($stripeSecretKey);

$postdata = file_get_contents("php://input");
$request = json_decode($postdata, true);

$subscriptionId = $request['subscription_id'];

try {
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
    $subscription->cancel();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
