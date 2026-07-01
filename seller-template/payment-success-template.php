<?php
/**
 * Template Name: Payment Success Template
 */

function uim_enqueue_admin_assets() {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
    wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
}
add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');

get_header(); ?>
<div class="container-padding-sc">
<?php
$path = plugin_dir_path(__DIR__);
require_once $path . 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('getenv('STRIPE_SECRET_KEY')');

if (!isset($_GET['session_id'])) {
    echo '<div class="container py-5 text-center"><div class="alert alert-danger">Invalid session. Please try again.</div></div>';
    get_footer();
    exit;
}

try {
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

    global $wpdb;
    $table = $wpdb->prefix . "warranty_payments";
    $warranty ="seller_purchaser_info";

    // Check DB record
    $payment = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE stripe_session_id = %s", $session->id)
    );
    $id = $payment->warranty_id;
    $table_name =  'webhook_log';
                        $results = $wpdb->get_results( "SELECT * FROM `{$table_name}`", ARRAY_A );
                        foreach ( $results as $payment ) {
                            if ( !empty($payment['request_data']) ) {
                                $dataObject = json_decode( $payment['request_data'] );
                                if ( json_last_error() !== JSON_ERROR_NONE ) {
                                    continue;
                                }
                            } else {
                                continue;
                            }
                            if ( isset($dataObject->data->object) ) {
                                $obj = $dataObject->data->object;
                                $paid = isset($obj->paid) ? var_export($obj->paid, true) : 'N/A';
                                $paymentIntent = isset($obj->payment_intent) ? $obj->payment_intent : 'N/A';
                            } else {
                                echo '<p>Expected data not found in JSON for ID ' . esc_html($payment['id']) . '</p>';
                            }
                        }
    

    if ($obj->paid === true && $paymentIntent === $session->payment_intent) {
            $wpdb->update(
                $table,
                [
                    'status' => 'paid',
                    'stripe_payment_id' => $session->payment_intent,
                ],
                ['stripe_session_id' => $session->id]
            );
            $wpdb->update(
                $warranty,
                [
                    'status' => 'paid',
                ],
                [ 'id' =>$id]
            );
        ?>
        <div class="container py-5 text-center">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="green" class="bi bi-check-circle mb-3" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z"/>
                            <path d="M10.97 4.97a.75.75 0 0 1 1.08 1.04l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 
                            3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                        </svg>
                    </div>
                    <h1 class="fw-bold text-success">Payment Successful!</h1>
                    <p class="lead mt-3">Thank you for your payment. Your transaction was completed successfully.</p>
                    <hr class="my-4">
                    <a href="https://elitewarrantyprogram.idestpro.com/warranty-details/?warranty_id=<?php echo $id?>" class="btn btn-success mt-4">My Warranties</a>
                </div>
            </div>
        </div>
        <?php
    } 
    elseif($paymentIntent === $session->payment_intent){
    $wpdb->update(
                $table,
                [
                    'status' => 'paid',
                    'stripe_payment_id' => $session->payment_intent,
                ],
                ['stripe_session_id' => $session->id]
            );
            $wpdb->update(
                $warranty,
                [
                    'status' => 'paid',
                ],
                [ 'id' =>$id]
            );
    ?>
    <div class="container py-5 text-center">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="green" class="bi bi-check-circle mb-3" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z"/>
                            <path d="M10.97 4.97a.75.75 0 0 1 1.08 1.04l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 
                            3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                        </svg>
                    </div>
                    <h1 class="fw-bold text-success">Payment Successful!</h1>
                    <p class="lead mt-3">Thank you for your payment. Your transaction was completed successfully.</p>
                    <hr class="my-4">
                    <a href="https://elitewarrantyprogram.idestpro.com/warranty-details/?warranty_id=<?php echo $id?>" class="btn btn-success mt-4">My Warranties</a>
                </div>
            </div>
        </div>
        
    <?}
    else {
        ?>
        <div class="container py-5 text-center">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="red" class="bi bi-x-circle mb-3" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 
                            .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 
                            8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 
                            8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                    <h1 class="fw-bold text-danger">Payment Pending</h1>
                    <p class="lead mt-3">Your payment is still processing. Please refresh this page after a moment.</p>
                    <a href="<?php echo site_url(); ?>/my-warranties/" class="btn btn-danger mt-4">Back to My Warranties</a>
                </div>
            </div>
        </div>
        <?php
    }
} catch (Exception $e) {
    echo '<div class="container py-5 text-center"><div class="alert alert-danger">Error: ' . esc_html($e->getMessage()) . '</div></div>';
}
?>
</div>
<?php get_footer(); ?>
