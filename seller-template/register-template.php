<?php 

add_filter('theme_page_templates', 'seller_register_templates');
function seller_register_templates($templates) {
    $templates['seller-registration-template.php'] = 'Seller Registration Template';
    $templates['seller-login-template.php'] = 'Seller Login Template';
    $templates['forgot-password-template.php'] = 'Forgot Password';
    $templates['edit-profile-template.php'] = 'Edit Profile Template';
    $templates['change-password-template.php'] = 'Change Password Template';
    $templates['seller-account-template.php'] = 'Seller Account Template';
    $templates['register-new-warranty-template.php'] = 'Register New Warranty Template';
    $templates['my-warranties-template.php'] = 'My Warranties Template';
    $templates['profile-settings-template.php'] = 'Profile Settings Template';
    $templates['reset-password-template.php'] = 'Reset Password';
     $templates['warranty-details-template.php'] = 'Warranty Details template';
     $templates['edit-warranty-template.php'] = 'Edit Warranty Template';
     $templates['process-payment-template.php'] = 'Process Payment Template';
     $templates['payment-success-template.php'] = 'Payment Success Template';
     $templates['payment-failed-template.php'] = 'Payment Faild Template';
     $templates['payment-history-template.php'] = 'Payment History Template';
     $templates['support-template.php'] = 'Support Template';
    return $templates;
}

add_filter('template_include', 'seller_load_template');
function seller_load_template($template) {
    if (is_page()) {
        $template_slug = get_page_template_slug();
        $plugin_templates = [
            'seller-registration-template.php',
            'seller-login-template.php',
            'seller-account-template.php',
            'forgot-password-template.php',
            'edit-profile-template.php',
            'change-password-template.php',
            'register-new-warranty-template.php',
             'my-warranties-template.php',
             'profile-settings-template.php',
              'reset-password-template.php',
              'warranty-details-template.php',
              'edit-warranty-template.php',
              'process-payment-template.php',
              'payment-success-template.php',
              'payment-failed-template.php',
              'payment-history-template.php',
              'support-template.php',
        ];

        if (in_array($template_slug, $plugin_templates)) {
            $plugin_template_path = plugin_dir_path(__DIR__) . 'seller-template/' . $template_slug;
            if (file_exists($plugin_template_path)) {
                return $plugin_template_path;
            }
        }
    }
    return $template;
}

function seller_create_pages() {
    $pages = [
        'seller-registration' => [
            'title' => 'Seller Registration',
            'template' => 'seller-registration-template.php'
        ],
        'retailer-login' => [
            'title' => 'Retailer login',
            'template' => 'seller-login-template.php'
        ],
        'retailer-account' => [
            'title' => 'Retailer Account',
            'template' => 'seller-account-template.php'
        ],
        'forgot-password' => [
            'title' => 'Forgot Password',
            'template' => 'forgot-password-template.php'
        ],
         'edit-profile' => [
            'title' => 'Edit Profile',
            'template' => 'edit-profile-template.php'
        ],
        'change-password' => [
            'title' => 'Change Password',
            'template' => 'change-password-template.php'
        ],
        'register-new-warranty' => [
            'title' => 'Register New Warranty',
            'template' => 'register-new-warranty-template.php'
        ],
        'my-warranties' => [
            'title' => 'My Warranties',
            'template' => 'my-warranties-template.php'
        ],
        'profile-settings' => [
            'title' => 'Profile Settings',
            'template' => 'profile-settings-template.php'
        ],
        'reset-password' => [
            'title' => 'Reset Password',
            'template' => 'reset-password-template.php'
        ],
        'warranty-details' => [
            'title' => 'Warranty Details',
            'template' => 'warranty-details-template.php'
        ],
        'edit-warranty' => [
            'title' => 'Edit Warranty',
            'template' => 'edit-warranty-template.php'
        ],
        'process-payment' => [
            'title' => 'Process Payment',
            'template' => 'process-payment-template.php'
        ],
        'payment-success' => [
            'title' => 'Payment Success',
            'template' => 'payment-success-template.php'
        ],
        'payment-failed' => [
            'title' => 'Payment Failed',
            'template' => 'payment-failed-template.php'
        ],
        'payment-history' => [
            'title' => 'Payment History',
            'template' => 'payment-history-template.php'
        ],
        'support' => [
            'title' => 'Support',
            'template' => 'support-template.php'
        ],
    ];

    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);
        if (!$existing_page) {
            wp_insert_post([
                'post_title'     => $page_data['title'],
                'post_name'      => $slug,
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'page_template'  => $page_data['template'],
            ]);
        }
    }
}


function create_seller_role() {
    add_role('reseller', 'Reseller', [
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false
    ]);
}
?>