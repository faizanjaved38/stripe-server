<?php

require_once '../vendor/autoload.php';
require_once '../secrets.php';
\Stripe\Stripe::setApiKey($stripeSecretKey);

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost:4242';

try {
  $postdata = file_get_contents("php://input");
  $request = json_decode($postdata);
  $prices = \Stripe\Price::all([
    // retrieve lookup_key from form data POST body
    'lookup_keys' => [$_POST['lookup_key']],
    'expand' => ['data.product']
  ]);
  $checkout_session = \Stripe\Checkout\Session::create([
    'line_items' => [[
      'price' => $prices->data[0]->id,
      'quantity' => 1,
    ]],
    'mode' => 'subscription',
    'success_url' => $YOUR_DOMAIN . '/success.html?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $YOUR_DOMAIN . '/checkout.html?hostname='.$_POST['hostname'],
    'metadata' => [
      'hostname' => $_POST['hostname']
    ]
  ]);
    // Redirect after successful insertion
    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
