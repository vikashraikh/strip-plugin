<?php
/**
 * Template Name: Process Payment Template
 */

$path = plugin_dir_path(__DIR__);
require_once $path . 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('getenv('STRIPE_SECRET_KEY')');
$warranty_id = intval($_GET['pay_warranty']);
if (!isset($_GET['pay_warranty'])) {
    wp_die("Invalid Request");
}
global $wpdb;
$table = 'seller_purchaser_info';
$plantable = 'warranty_plans';
$warranty = $wpdb->get_row(
  $wpdb->prepare(
    "SELECT w.*, 
                p.id AS plan_id, 
                p.name AS plan_name, p.price as price,
                p.description AS plan_description
         FROM $table AS w
         INNER JOIN $plantable AS p ON w.plan_type = p.id
         WHERE w.id = %d",
    $warranty_id
  )
);

$current_user_id = get_current_user_id();
$amount = $warranty->price; 
$fname = $warranty->first_name; 
$lname = $warranty->last_name;
$productname=$warranty->plan_name;
$costomer_mail= $warranty->email;

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' =>$productname ,
            ],
            'unit_amount' => $amount * 100,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    // 'customer_email' => $costomer_mail,
    'success_url' => site_url('/payment-success/?session_id={CHECKOUT_SESSION_ID}'),
    'cancel_url' => site_url('/payment-failed/?session_id={CHECKOUT_SESSION_ID}'),
]);
global $wpdb;
$table = $wpdb->prefix . "warranty_payments";
$wpdb->insert($table, [
    'warranty_id' => $warranty_id,
    'user_id' => $current_user_id,
    'amount' => $amount,
    'user_name' => $fname,
    'user_last_name' => $lname,
    'protection_plan_type'=>$productname,
    'currency' => 'usd',
    'stripe_session_id' => $session->id,
    'status' => 'pending',
]);

wp_redirect($session->url);
exit;
