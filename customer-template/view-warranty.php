<?php 
global $wpdb;
$table = 'seller_purchaser_info';
// Get warranty record
$warranty = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
if (!$warranty) {
    wp_die('Warranty not found');
}

$plugin_url = plugin_dir_url(__DIR__);
?>

<body>
<link rel="stylesheet" href="<?php echo $plugin_url ?>css/bootstrap.min.css"/>
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<div class="container mt-4 custom-container">
    <div class="warranty-card">
    <h3 class="text-center mb-4">Warranty Details</h3>

    <div class="detail-row">
        <div class="detail-label">Warranty Number:</div>
        <div class="detail-value"><?php echo esc_html($warranty->id); ?></div>
    </div>

    <div class="detail-row">
        <?php  
            $saved_product_ids = !empty($warranty->plan_type)  
                ? array_map('intval', explode(',', $warranty->plan_type))  
                : array();
            
            // Fetch plan names only if IDs exist
            if (!empty($saved_product_ids)) {
                $placeholders = implode(',', array_fill(0, count($saved_product_ids), '%d'));
                $query = $wpdb->prepare(
                    "SELECT name FROM warranty_plans WHERE id IN ($placeholders)",
                    ...$saved_product_ids
                );
                $saved_products = $wpdb->get_results($query);
            } else {
                $saved_products = array();
            }
            ?>
        <div class="detail-label">Protection Plan Type:</div>
        <div class="detail-value">
           <?php if (!empty($saved_products)) : ?>
            <ul class="mb-0">
                <?php foreach ($saved_products as $product) : ?>
                    <li><?php echo esc_html($product->name); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <span>-</span>
        <?php endif; ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Customer Name:</div>
        <div class="detail-value">
            <?php echo esc_html(ucfirst($warranty->first_name) . ' ' . ucfirst($warranty->last_name)); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Customer Address:</div>
        <div class="detail-value">
            <?php
            $address_parts = array_filter([
                $warranty->address_line1,
                $warranty->address_line2,
                $warranty->city,
                $warranty->state,
                $warranty->zip
            ]);
            echo esc_html(implode(', ', $address_parts));
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Customer Contact Phone:</div>
        <div class="detail-value"><?php echo esc_html($warranty->phone); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Customer Email:</div>
        <div class="detail-value"><?php echo esc_html($warranty->email); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">In Which Room Installation Has Been Done:</div>
        <div class="detail-value"><?php echo esc_html($warranty->room); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Installed Countertop Date:</div>
        <div class="detail-value"><?php echo esc_html($warranty->install_date); ?></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Payment Status:</div>
        <div class="detail-value">
            <?php 
            echo esc_html( ucfirst($warranty->status) );
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Submitted At:</div>
        <div class="detail-value"> <?php echo esc_html( date('M j, Y', strtotime($warranty->submitted_at)) ); ?></div>
    </div>
  </div>

    <div class="text-center mt-4">
        <a href="<?php echo admin_url('admin.php?page=warranty_list'); ?>" class="btn btn-back plan-btn">BACK</a>
    </div>
</div>

<style>
.warranty-card {
    max-width: 850px;
    margin: 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}
.warranty-card .detail-row {
    display: flex;
    border: 1px solid #e5e5e5;
}
.warranty-card .detail-row:first-of-type {
    border-top: 1px solid #e5e5e5;
}
.warranty-card .detail-label {
    width: 35%;
    padding: 12px 15px;
    font-weight: 600;
    background: #f9f9f9;
    border-right: 1px solid #e5e5e5;
}
.warranty-card .detail-value {
    flex: 1;
    padding: 12px 15px;
}
.warranty-card h3 {
    font-size: 22px;
    font-weight: 600;
}
.detail-value ul{padding-left:0px;}
</style>


</div>
</body>
</html>