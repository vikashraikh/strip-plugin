<?php
/*
Plugin Name: Warranty Program
Description: customer warranty.
Version: 1.0.0
Author: developer
*/
include 'ajax.php';
$plugin_url = plugin_dir_url(__FILE__);
add_action('admin_enqueue_scripts', 'uim_enqueue_admin_asset');
function uim_enqueue_admin_asset() {
    global $plugin_url;
    wp_enqueue_style('datatable-css', $plugin_url . 'css/jquery.dataTables.min.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('datatable-js', $plugin_url . 'js/jquery.dataTables.min.js', ['jquery'], null, true);
}



include plugin_dir_path(__FILE__) . 'includes/common-function.php';
include plugin_dir_path(__FILE__) . 'includes/sidebar.php';
require_once plugin_dir_path(__FILE__) . 'customer-template/register-template.php';
require_once plugin_dir_path(__FILE__) . 'seller-template/register-template.php';
require_once plugin_dir_path(__FILE__) . 'includes/database.php';
register_activation_hook(__FILE__, 'create_customers_table');
register_activation_hook(__FILE__, 'my_plugin_create_page');
register_activation_hook(__FILE__, 'seller_create_pages');
register_activation_hook(__FILE__, 'create_seller_purchaser_table');
register_activation_hook(__FILE__, 'create_seller_role');


function warranty() {
    // Main menu
    add_menu_page('Warranty Program', 'Warranty Program','manage_options', 'warranty_program','warranty_program','dashicons-editor-paste-word', 25 );
    // Submenus
    add_submenu_page('warranty_program', 'Warranty', 'Warranty', 'manage_options', 'warranty_list', 'warranty_list');
     add_submenu_page(null,'', 'View Warranty ', 'manage_options', 'view_warranty', 'view_warranty');
    add_submenu_page('warranty_program', 'Claims', 'Claims', 'manage_options', 'claims', 'claims');
     add_submenu_page(null,'', 'View Claim ', 'manage_options', 'view_claim', 'view_claim');
    add_submenu_page('warranty_program', 'Manage Retailer', 'Manage Retailer', 'manage_options', 'retailers', 'retailers');
     add_submenu_page(null,'', 'Add Retailer', 'manage_options', 'add_retailer', 'add_retailer');
     add_submenu_page(null,'', 'Edit Retailer ', 'manage_options', 'edit_retailer', 'edit_retailer');
     add_submenu_page(null,'', 'View Retailer ', 'manage_options', 'view_retailer', 'view_retailer');
    add_submenu_page('warranty_program', 'Plans', 'Plans', 'manage_options', 'plans', 'plans');
     add_submenu_page(null,'', 'Add Plan ', 'manage_options', 'add_plan', 'add_plan');
     add_submenu_page(null,'', 'Edit Plan ', 'manage_options', 'edit_plan', 'edit_plan');
     add_submenu_page(null,'', 'View Plan ', 'manage_options', 'view_plan', 'view_plan');
     add_submenu_page('warranty_program', 'Transactions', 'Transactions', 'manage_options', 'transactions', 'transactions');
  //  add_submenu_page('warranty_program', 'Reports', 'Reports', 'manage_options', 'reports', 'reports');
}
add_action('admin_menu', 'warranty');


function warranty_program() {
    include plugin_dir_path(__FILE__) . 'program-template/warranty_program.php';
}
function warranty_list(){
    include plugin_dir_path(__FILE__) . 'program-template/warranty-list.php';
}
function view_warranty() {
   global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        wp_die('Invalid ID');
    }

    $id = intval($_GET['id']);
    $table = $wpdb->prefix . 'seller_purchaser_info';

    include plugin_dir_path(__FILE__) . 'customer-template/view-warranty.php';
}
function claims(){
    include plugin_dir_path(__FILE__) . 'program-template/customer.php';
}
function view_claim() {
    global $wpdb;
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        wp_die('Invalid ID');
    }
    $id = intval($_GET['id']);
    $table = 'warranty_claims';
    include plugin_dir_path(__FILE__) . 'customer-template/view-claims.php';
}
function retailers(){
    include plugin_dir_path(__FILE__) . 'program-template/retailers.php';
}
function add_retailer() {
    global $wpdb;
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }
    $id = intval( $_GET['id'] );
    $table = 'become_authorized_retailer';
    include plugin_dir_path(__FILE__) . 'customer-template/add-retailer.php';
}
function edit_retailer() {
    global $wpdb;
     if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        wp_die('Invalid ID');
    }
    $id = intval($_GET['id']);
    $table = 'become_authorized_retailer';
    include plugin_dir_path(__FILE__) . 'customer-template/edit-retailer.php';
}
function view_retailer() {
    global $wpdb;
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        wp_die('Invalid ID');
    }
    $id = intval($_GET['id']);
    $table = 'become_authorized_retailer';
    include plugin_dir_path(__FILE__) . 'customer-template/view-retailer.php';
   
}
function plans(){
    include plugin_dir_path(__FILE__) . 'program-template/plan-list.php';
}
function add_plan() {
    global $wpdb;
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }
    $id = intval( $_GET['id'] );
    $table = 'warranty_plans';
    include plugin_dir_path(__FILE__) . 'customer-template/add-plan.php';
}

function edit_plan() {
    global $wpdb;
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }
    $id = intval( $_GET['id'] );
    $table = 'warranty_plans';
    include plugin_dir_path(__FILE__) . 'customer-template/edit-plan.php';
}
function view_plan() {
    global $wpdb;
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }
    $id = intval( $_GET['id'] );
    $table = 'warranty_plans';
    include plugin_dir_path(__FILE__) . 'customer-template/view-plan.php';
}
function transactions(){
    include plugin_dir_path(__FILE__) . 'program-template/manage-payments.php';
}
add_action('wp_ajax_filter_warranty_by_date', 'filter_warranty_by_date_callback');

function filter_warranty_by_date_callback(){
    global $wpdb;
    $table = 'seller_purchaser_info';

    $day   = sanitize_text_field($_POST['day']);
    $month = sanitize_text_field($_POST['month']);

    $where = "1=1";

    if (!empty($day)) {
    $where .= $wpdb->prepare(" AND DATE(submitted_at) = %s", $day); 
        // $day must be in YYYY-MM-DD format (e.g. 2025-08-23)
    }

    if (!empty($month)) {
    $where .= $wpdb->prepare(" AND DATE_FORMAT(submitted_at, '%%Y-%%m') = %s", $month); 
    // $month must be in YYYY-MM format (e.g. 2025-08)
    }

    $results = $wpdb->get_results("SELECT * FROM $table WHERE $where ORDER BY id DESC");

    if(empty($results)){
        wp_send_json_error(['message' => 'No records found for selected date/month.']);
    }

    // build new rows
    $html = '';
    foreach($results as $row){
        $url = admin_url('admin.php?page=view_warranty&id=' . $row->id);

        // plan type
        $saved_product_ids = !empty($row->plan_type) ? array_map('intval', explode(',', $row->plan_type)) : array();
        if (!empty($saved_product_ids)) {
            $plan_ids = implode(',', array_fill(0, count($saved_product_ids), '%d'));
            $query = $wpdb->prepare("SELECT name FROM warranty_plans WHERE id IN ($plan_ids)", ...$saved_product_ids);
            $saved_products = $wpdb->get_results($query);
        } else {
            $saved_products = array();
        }

        $plans_html = '';
        foreach($saved_products as $product){
            $plans_html .= '<li style="margin-bottom:5px;">'.esc_html($product->name).'</li>';
        }

        $html .= '<tr>
            <td>'.esc_html($row->id).'</td>
            <td>'.esc_html(ucwords(trim($row->first_name.' '.$row->last_name))).'</td>
            <td>'.esc_html($row->email).'</td>
            <td>'.esc_html($row->phone).'</td>
            <td><ol style="list-style:none;margin-left:5px;">'.$plans_html.'</ol></td>
            <td><a class="action_btn" href="'.esc_url($url).'"><img src="/wp-content/plugins/warranty-program/images/icons/view.svg" title="View" /></a></td>
        </tr>';
    }

    wp_send_json_success(['html' => $html]);
}
