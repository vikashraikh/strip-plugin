<?php
function delete_retailer() {
    if ( ! current_user_can('manage_options') ) {
        wp_die('Unauthorized');
    }

    global $wpdb;
    $table = 'become_authorized_retailer';
    $uid = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($uid > 0) {
        $user_id = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $uid) );
        $res = $wpdb->delete($table, ['id' => $uid]);
        if ($res && $user_id) {
        require_once(ABSPATH . 'wp-admin/includes/user.php'); // needed for wp_delete_user
        wp_delete_user($user_id);
        }
        if ($res) {
            wp_redirect(admin_url('admin.php?page=retailers&msg=deleted'));
        } else {
            wp_redirect(admin_url('admin.php?page=retailers&msg=fail'));
        }
        exit;
    }

    wp_redirect(admin_url('admin.php?page=retailers&msg=invalid'));
    exit;
}
add_action('admin_post_delete_retailer', 'delete_retailer');

function delete_plan() {
     if ( ! current_user_can('manage_options') ) {
        wp_die('Unauthorized');
    }
    global $wpdb;
    $table = 'warranty_plans';
    $uid = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($uid > 0) {
        $res = $wpdb->delete($table, ['id' => $uid]);

        if ($res) {
            wp_redirect(admin_url('admin.php?page=plans&msg=deleted'));
        } else {
            wp_redirect(admin_url('admin.php?page=plans&msg=fail'));
        }
        exit;
    }
}
add_action( 'admin_post_delete_plan', 'delete_plan' );


add_action('template_redirect', function() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (is_page('retailer-login')) {
            if (in_array('administrator', (array) $user->roles)) {
                wp_safe_redirect(admin_url()); 
                exit;
            } else {
                wp_safe_redirect(site_url('/retailer-account/'));
                exit;
            }
        }
    }
});


add_action('after_setup_theme', function() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
});



add_action('rest_api_init', function () {
    register_rest_route('myplugin/v1', '/stripe-webhook-log', array(
        'methods'  => 'POST',
        'callback' => 'myplugin_stripe_webhook_log_handler',
        'permission_callback' => '__return_true', 
    ));
});

function myplugin_stripe_webhook_log_handler(WP_REST_Request $request) {
    global $wpdb;
    $endpoint_secret = 'getenv('STRIPE_WEBHOOK_SECRET')'; 
    // $payload = $request->get_body();
    // $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    try {
        $current_user_id = $warranty->id;
        $rawData  = $request->get_body();
        $jsonData = json_decode($rawData, true);
        $current_user_id = get_current_user_id();

        $table = "webhook_log"; 
        $results = $wpdb->insert(
            $table,
            array(
                'user_id'      => $current_user_id,
                'request_data' => wp_json_encode($jsonData), 
                'response_data'=> $rawData,
                'created_at'   => current_time('mysql') 
            )
        );
        
      //  $results = stripePaymentUpdate($jsonData);
    } catch (Exception $e) {
        return new WP_REST_Response(
            array('error' => $e->getMessage()),
            400
        );
    }
    
    echo "<pre>";
    print_r($results);
    die();

    return new WP_REST_Response(array('status' => 'success'), 200);
}


// function stripePaymentUpdate($jsonData) {
//     global $wpdb;
//     $payment_intent_id = $jsonData['data']['object']['payment_intent'] ?? null;
//     $payment_status = $jsonData['data']['object']['paid'];

//     if (!$payment_intent_id) {
//         error_log('Payment Intent ID not found in the webhook data.');
//         return;
//     }
//     $table_name = $wpdb->prefix . 'warranty_payments';
//     $query = $wpdb->prepare(
//         "SELECT * FROM {$table_name} WHERE stripe_payment_id = %s",
//         $payment_intent_id
//     );
//     $warrantyPayments = $wpdb->get_row($query);
//     if($payment_status){
//         $payment_status = "paid";
//     }else{
//         $payment_status = "unpaid";
//     }
    
//     $table = $wpdb->prefix . "warranty_payments";
//     $wpdb->update(
//         $table,
//         [
//             'status' => $payment_status,
//         ],
//         ['stripe_payment_id' =>$payment_intent_id]
//     );
    
//     return true;
//     if ($warrantyPayments) {
//         error_log('Payment found: ' . print_r($warrantyPayments, true));
//     } else {
//         error_log('No matching payment record found for Payment Intent ID: ' . $payment_intent_id);
//     }
// }



