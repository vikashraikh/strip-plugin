<?php

function create_customers_table() {
    global $wpdb;

    $table_name = 'warranty_claims'; 
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        plan_type VARCHAR(100),
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        address_line1 TEXT,
        address_line2 TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        zip VARCHAR(20),
        phone VARCHAR(50),
        email VARCHAR(100),
        plan_number VARCHAR(100),
        fabricator VARCHAR(100),
        countertop_type VARCHAR(100),
        room VARCHAR(50),
        problem TEXT,
        chip_at_sink VARCHAR(10),
        description TEXT,
        damage_during_delivery VARCHAR(10),
        install_date TEXT,
        damage_date TEXT,
        attempt_clean VARCHAR(10),
        damage_photos longtext,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function create_seller_purchaser_table() {
    global $wpdb;
    $table_name ='seller_purchaser_info';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        seller_id BIGINT UNSIGNED NOT NULL,
        plan_type VARCHAR(100),
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        address_line1 TEXT,
        address_line2 TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        zip VARCHAR(20),
        phone VARCHAR(50),
        email VARCHAR(100),
        plan_number VARCHAR(100),
        fabricator VARCHAR(100),
        countertop_type VARCHAR(100),
        room VARCHAR(50),
        problem TEXT,
        chip_at_sink VARCHAR(10),
        description TEXT,
        status TEST,
        damage_during_delivery VARCHAR(10),
        install_date TEXT,
        damage_date TEXT,
        attempt_clean VARCHAR(10),
        damage_photos longtext,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_seller_id (seller_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

