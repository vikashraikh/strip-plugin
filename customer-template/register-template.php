<?php
add_filter('theme_page_templates', 'my_plugin_register_template');
function my_plugin_register_template($templates) {
    $templates['customer-template/customer-template.php'] = 'Customer Template';
    return $templates;
}

add_filter('template_include', 'my_plugin_load_template');
function my_plugin_load_template($template) {
    if (is_page()) {
        $page_template = get_page_template_slug(get_queried_object_id());
        if ($page_template === 'customer-template/customer-template.php') {
            return plugin_dir_path(__DIR__) . 'customer-template/customer-template.php';
        }
    }
    return $template;
}

function my_plugin_create_page() {
    $page_slug  = 'warranty-form';
    $page_title = 'Warranty Form';

    $existing = get_page_by_path($page_slug);

    if (!$existing) {
        wp_insert_post([
            'post_title'     => $page_title,
            'post_name'      => $page_slug,
            'post_content'   => '',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'page_template'  => 'customer-template/customer-template.php', 
        ]);
    }
}