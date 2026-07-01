<?php
global $wpdb;
$plugin_url = plugin_dir_url(__DIR__);
$table = 'warranty_plans';

// Get plan data by ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    wp_die('Invalid plan ID.');
}

$plan_id = intval($_GET['id']);
$plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $plan_id));

if (!$plan) {
    wp_die('Plan not found.');
}
?>
<?php $siteurl = get_option('siteurl'); ?>
<body class="custom-wrap">
<link rel="stylesheet" href="<?php echo esc_url($plugin_url); ?>css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<div class="container mt-4 custom-container">
    <div class="claim-card">
    <h3 class="text-center mb-4">Plan Details</h3>

    <div class="detail-row">
        <div class="detail-label">Plan Name</div>
        <div class="detail-value"><?php echo esc_html($plan->name); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Description</div>
        <div class="detail-value"><?php echo nl2br(esc_html($plan->description)); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Price</div>
        <div class="detail-value">
            <?php echo '$'.number_format((float)$plan->price, 2); ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value"><?php echo $plan->status == 1 ? 'Active' : 'Inactive'; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Created At</div>
        <div class="detail-value"><?php echo date("M j, Y", strtotime($plan->created_at)); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Updated At</div>
        <div class="detail-value"><?php echo date("M j, Y", strtotime($plan->updated_at)); ?></div>
    </div>

    <div class="text-center mt-4">
      <a href="<?php echo admin_url('admin.php?page=plans'); ?>" class="btn btn-back plan-btn">Back</a>
    </div>
</div>

<style>
.claim-card {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}
.claim-card .detail-row {
    display: flex;
    border: 1px solid #e5e5e5;
}
.claim-card .detail-row:first-of-type {
    border-top: 1px solid #e5e5e5;
}
.claim-card .detail-label {
    width: 35%;
    padding: 12px 15px;
    font-weight: 600;
    background: #f9f9f9;
    border-right: 1px solid #e5e5e5;
}
.claim-card .detail-value {
    flex: 1;
    padding: 12px 15px;
}
.claim-card h3 {
    font-size: 22px;
    font-weight: 600;
}
</style>

</body>