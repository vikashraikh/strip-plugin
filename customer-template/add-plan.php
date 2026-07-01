<?php
global $wpdb;
$plugin_url = plugin_dir_url(__DIR__);
$table = 'warranty_plans'; 
// Save form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize_text_field($_POST['name']);
    $description = sanitize_textarea_field($_POST['description']);
    $price       = floatval($_POST['price']);
    $status      = intval($_POST['status']);

    $wpdb->insert(
        $table,
        [
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'status'      => $status,
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql')
        ],
        ['%s', '%s', '%f', '%d', '%s', '%s']
    );
    echo '<div class="container mt-4 notice notice-success" style="padding:10px; background:#d4edda; color:#155724;">Plan saved successfully!</div>';
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
    max-width: 700px;
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
</style>
<div class="container mt-4 custom-container add-plan">
    <form method="post" class="mb-5">
        <h3 class="text-center mb-4">Add Plan</h3>
        <table class="table table-bordered">
            <tr>
                <th><label for="name">Plan Name</label></th>
                <td><input type="text" id="name" name="name" class="form-control" required></td>
            </tr>
            <tr>
                <th><label for="description">Description</label></th>
                <td><textarea id="description" name="description" class="form-control" rows="4"></textarea></td>
            </tr>
            <tr>
                <th><label for="price">Price ($)</label></th>
                <td><input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="active" name="status" value="1" class="form-check-input" checked>
                        <label for="active" class="form-check-label">Active</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="inactive" name="status" value="0" class="form-check-input">
                        <label for="inactive" class="form-check-label">Inactive</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    <button type="submit" class="btn auto_btn plan-btn">Save Plan</button>
                    <a href="<?php echo admin_url('admin.php?page=plans'); ?>" class="btn auto_btn plan-btn back-btn">Back</a>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
