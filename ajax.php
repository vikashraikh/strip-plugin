<?php

function uim_enqueue_admin_assets1() {
   $plugin_url = plugin_dir_url(__FILE__);

    wp_enqueue_style('uim-select2-css', $plugin_url . 'css/select2.min.css');
    wp_enqueue_script('select2-js', $plugin_url . 'js/select2.min.js', array('jquery'), null, true);
     wp_enqueue_script('country-ajax', $plugin_url . 'js/ajax.js', array('jquery'), null, true);

     wp_localize_script('country-ajax', 'country_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('country_search_nonce'),
        'plan_nonce'        => wp_create_nonce('plan_validation'),
         'warranty_nonce' => wp_create_nonce('warranty_stats_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets1');

function handle_search_country() {
    error_log('handle_search_country() called');

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'country_search_nonce')) {
        error_log('Invalid nonce');
        wp_send_json_error(['message' => 'Invalid nonce'], 400);
    }

    global $wpdb;
    $table = 'geo_countries';
    $search = sanitize_text_field($_POST['q'] ?? '');
    $results = [];

    error_log("Searching in: $table for: $search");

    if (!empty($search)) {
        $countrys = $wpdb->get_results($wpdb->prepare(
            "SELECT country_id, country_name 
             FROM $table 
             WHERE country_name LIKE %s 
             ORDER BY country_name ASC 
             LIMIT 20",
            '%' . $wpdb->esc_like($search) . '%'
        ));
    } else {
        $countrys = $wpdb->get_results(
            "SELECT country_id, country_name 
             FROM $table 
             ORDER BY country_name ASC 
             LIMIT 5"
        );
    }

    foreach ($countrys as $country) {
        $results[] = [
            'id'   => $country->country_name,  
            'text' => $country->country_name
        ];
    }

    error_log('Returning results: ' . print_r($results, true));
    wp_send_json($results);
}
add_action('wp_ajax_search_country', 'handle_search_country');        
add_action('wp_ajax_nopriv_search_country', 'handle_search_country');

add_action('wp_ajax_validate_plan_number', 'validate_plan_number');
add_action('wp_ajax_nopriv_validate_plan_number', 'validate_plan_number');

function validate_plan_number() {
    $warrnties = 'seller_purchaser_info';
  check_ajax_referer('plan_validation', 'nonce');
  $plan = intval($_POST['plan_number']);
  global $wpdb;
  $exists = $wpdb->get_var(
  $wpdb->prepare(
    "SELECT COUNT(*) FROM $warrnties WHERE id = %d AND status = %s",
    $plan,'paid'
  )
);

  if ($exists) {
    wp_send_json_success();
  } else {
    wp_send_json_error();
  }
}

add_action('wp_ajax_get_warranty_stats', 'get_warranty_stats');
add_action('wp_ajax_nopriv_get_warranty_stats', 'get_warranty_stats');

function get_warranty_stats() {
    check_ajax_referer('warranty_stats_nonce', 'nonce');
    global $wpdb;
    $seller_id = get_current_user_id();
    $table = 'seller_purchaser_info'; 
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);

    if (!$start_date || !$end_date) {
        wp_send_json_error(['message' => 'Invalid date range']);
    }
    if (!$seller_id) {
        wp_send_json_error(['message' => 'User not logged in']);
    }
    $count = $wpdb->get_var(
        $wpdb->prepare("
            SELECT COUNT(*) 
            FROM $table 
            WHERE reseller_id = %d 
            AND DATE(submitted_at) BETWEEN %s AND %s
        ", $seller_id, $start_date, $end_date)
    );

    if ($count !== null) {
        wp_send_json_success(['count' => intval($count)]);
    } else {
        wp_send_json_error(['message' => 'No data found']);
    }
}




