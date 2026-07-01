<?php
/**
 * Template Name: Payment Faild Template
 */

function uim_enqueue_admin_assets() {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
    wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
}
add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');

get_header(); 

require_once plugin_dir_path(__DIR__) . 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('getenv('STRIPE_SECRET_KEY')');

$session_id = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';


    

if ($session_id) {
    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        if (!empty($session->id)) {
            global $wpdb;
            $warranty ="seller_purchaser_info";
            $table = $wpdb->prefix . 'warranty_payments';
            $result = $wpdb->update(
                $table,
                ['status' => 'cancelled'],        
                ['stripe_session_id' => $session->id]
            );
            
            $payment = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE stripe_session_id = %s", $session->id)
            );

           $id = $payment->warranty_id;
           $wpdb->update(
                $warranty,
                [
                    'status' => 'cancelled',
                ],
                [ 'id' =>$id]
            );
        }

    } catch (\Exception $e) {
        echo '<p class="text-danger">Stripe error: ' . esc_html($e->getMessage()) . '</p>';
    }
} else {
    echo '<p class="text-danger">No session_id found in URL.</p>';
}
?>
<div class="container-padding-sc">
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
                    <h1 class="fw-bold text-danger">Payment Faild</h1>
                    <a href="<?php echo site_url(); ?>/warranty-details/?warranty_id=<?php echo $id?>" class="btn btn-danger mt-4">Back to warranty</a>
                </div>
            </div>
        </div>

</div>
<?php get_footer(); ?>
