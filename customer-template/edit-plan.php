<?php
wp_head();
global $wpdb;
$plugin_url = plugin_dir_url(__DIR__);
$table      = 'warranty_plans';

// Get the ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the existing record
$plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

if (!$plan) {
    wp_die('Plan not found.');
}

// Update form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize_text_field($_POST['name']);
    $description = sanitize_textarea_field($_POST['description']);
    $price       = floatval($_POST['price']);
    $status      = intval($_POST['status']);

    $wpdb->update(
        $table,
        [
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'status'      => $status,
            'updated_at'  => current_time('mysql')
        ],
        ['id' => $id],
        ['%s', '%s', '%f', '%d', '%s'],
        ['%d']
    );

    echo '<div class="container mt-4 notice notice-success" style="padding:10px; background:#d4edda; color:#155724;">Plan updated successfully!</div>';

    // Refresh data
    $plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
}
?>
<body class="custom-wrap">
<link rel="stylesheet" href="<?php echo esc_url($plugin_url); ?>css/bootstrap.min.css" />
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<style>
    .add-plan .form-check-input[type=radio]{position: relative;top: 10px;}
    .add-plan .form-check-input:checked {
    background-color: transparent;
    border-color: #2a4875;
}
 .custom-container form {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}
.custom-container form h3 {
    font-size: 22px;
    font-weight: 600;font-family: var(--bs-font-sans-serif);text-transform: none;
}
.custom-container input[type=radio]{margin-top:5px;}
.custom-container input[type=radio]:before{display:none;}
</style>
<div class="container mt-4 custom-container">
    <form method="post" class="mb-5">
         <h3 class="text-center mb-4">Edit Plan</h3>
        <table class="table table-bordered">
            <tr>
                <th><label for="name">Plan Name</label></th>
                <td><input type="text" id="name" name="name" class="form-control" value="<?php echo esc_attr($plan->name); ?>" required></td>
            </tr>
            <tr>
                <th><label for="description">Description</label></th>
                <td><textarea id="description" name="description" class="form-control" rows="8"><?php echo esc_textarea($plan->description); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="price">Price ($)</label></th>
                <td><input type="number" id="price" name="price" class="form-control" step="0.01" min="0" value="<?php echo esc_attr($plan->price); ?>" required></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="active" name="status" value="1" class="form-check-input" <?php checked($plan->status, 1); ?>>
                        <label for="active" class="form-check-label">Active</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="inactive" name="status" value="0" class="form-check-input" <?php checked($plan->status, 0); ?>>
                        <label for="inactive" class="form-check-label">Inactive</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    <button type="submit" class="btn auto_btn plan-btn">Update Plan</button>
                    <a href="<?php echo admin_url('admin.php?page=plans'); ?>" class="btn btn-back plan-btn">Back</a>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>