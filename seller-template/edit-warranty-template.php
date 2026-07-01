<?php
  /**
   * Template Name: Edit Warranty Template
   */
   function uim_enqueue_admin_assets()
  {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
     wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
      wp_enqueue_style('style-css1', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
    wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-bundle', $plugin_url . '/js/bootstrap.bundle.min.js' ,array('jquery'), null, true);
    wp_enqueue_script('canender', $plugin_url . '/js/jquery-ui.min.js' ,array('jquery'), null, true);
    wp_enqueue_script('warrant-form', $plugin_url . 'js/warranty-form.js', array('jquery'), null, true);
  }
  add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');
  get_header();
 ?>
<?php
if (!is_user_logged_in()) {
     wp_redirect(site_url('/retailer-login/')); 
    exit;
}
global $wpdb;
$table = 'seller_purchaser_info';
$plantable = 'warranty_plans'; 
$massage= [];
$error=[];
$warranty_id = intval($_GET['warranty_id']);
$warranty = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT w.*, 
                p.id AS plan_id, 
                p.name AS plan_name, p.price as price,
                p.description AS plan_description
         FROM $table AS w
         INNER JOIN $plantable AS p ON w.plan_type = p.id
         WHERE w.id = %d",
        $warranty_id
    )
);
if (!$warranty) {
    echo '<div class="alert alert-danger">Record not found.</div>';
    exit;
};
$room=sanitize_text_field($_POST['room']);
    $otherroom = !empty($_POST['other_room']) ? sanitize_text_field($_POST['other_room']) : '';
    $finalroom = ($room === "Other" && !empty($otherroom )) ? $otherroom  : $room;
// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_warranty'])) {
     $insert = $wpdb->update($table, [
        'plan_type'              => sanitize_text_field($_POST['plan_type']),
        'first_name'             => sanitize_text_field($_POST['customer_first_name']),
        'last_name'              => sanitize_text_field($_POST['customer_last_name']),
        'address_line1'          => sanitize_text_field($_POST['address_line1']),
        'address_line2'          => sanitize_text_field($_POST['address_line2']),
        'city'                   => sanitize_text_field($_POST['city']),
        'state'                  => sanitize_text_field($_POST['state']),
        'zip'                    => sanitize_text_field($_POST['zip']),
        'phone'                  => sanitize_text_field($_POST['customer_phone']),
        'email'                  => sanitize_email($_POST['customer_email']),
        'room'                   => $room,
        'install_date'           => sanitize_text_field($_POST['install_date']),
        'submitted_at'           => current_time('mysql'),
    ], ['id' => $warranty_id]);
    if( $insert){
        $massage[]= 'Warranty updated';
        wp_redirect(add_query_arg(['warranty_id' => $warranty_id], site_url('/warranty-details')));
    }
    else{
        $error[]='Warranty not updated' ;
    }
 $warranty = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT w.*, 
                p.id AS plan_id, 
                p.name AS plan_name, p.price as price, 
                p.description AS plan_description
         FROM $table AS w
         INNER JOIN $plantable AS p ON w.plan_type = p.id
         WHERE w.id = %d",
        $warranty_id
    )
);
}
?>
<style>
    .margin-custom{
      margin-top: 140px;
    }
</style>
<div class="container margin-custom mb-5">
  <?php
        foreach ($massage as $mass) :?>
  <div class="alert alert-success"> <?= esc_html($mass) ?> </div>
  <?php endforeach ;
        foreach ($error as $err) :
        ?>
  <div class="alert alert-danger"> <?= esc_html( $err) ?> </div>
  <?php endforeach; ?>
  <!-- <h1 class="text-center mb-5">Protection Plan Claim Form</h1> -->
  <div class="row justify-content-center elite-custom-account">
    <div class="col-md-3 col-lg-3 ">
      <?php sidebar() ?>
    </div>
    <div class="col-md-9 col-lg-9 form_bg">
      <div class="elite-custom-top text-center mb-5">
        <h1>Edit Warranty</h1>
      </div>
      <form method="post" id="warranty-form"  action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"
        enctype="multipart/form-data" class="needs-validation" novalidate>
        <?php wp_nonce_field('add_seller_warranty', '_warranty_nonce'); ?>
        <div class="row">
          <div class="col-12 valid  mb-3">
            <label for="plan_type" class="form-label">Select your Protection Plan type.<span
                class="text-danger">*</span></label>
                <?php 
            global $wpdb;
            $plantable = 'warranty_plans';
              $warrantyplans = $wpdb->get_results("SELECT * FROM $plantable");
            ?>   
            <select class="form-select" id="plan_type" name="plan_type" required>
              <option value="<?= esc_attr($warranty->plan_id) ?>"><?= esc_attr($warranty->plan_name) ?></option>
              <?php  foreach ($warrantyplans as $warrantyplan) { ?>
              <option value="<?php echo  esc_attr($warrantyplan->id) ?>"><?php echo  esc_attr ($warrantyplan->name)?></option>
              <?php } ?>
            </select>
            <div class="invalid-feedback">Please Select your Protection Plan type</div>
          </div>
          <div class="col-12 mb-12"> <label for="customer_first_name" class="form-label">Customer Name<span
                class="text-danger">*</span></label></div>
          <div class="col-6 mb-6">
            <input class="form-control" type="text" id="customer_first_name" value="<?= esc_attr($warranty->first_name) ?>" name="customer_first_name" required />
            <small class="form-text text-muted">First</small>
            <div class="invalid-feedback">Please Enter Your First Name</div>
          </div>
          <div class="col-6 mb-6">
            <input class="form-control" type="text" id="customer_last_name" value="<?= esc_attr($warranty->last_name) ?>" name="customer_last_name" required />
            <small class="form-text text-muted">Last</small>
            <div class="invalid-feedback">Please Enter Your First Name</div>
          </div>
          <div class="col-12 mt-3">
            <label class="form-label" for="address_line1">Customer Address <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="address_line1" value="<?= esc_attr($warranty->address_line1) ?>" name="address_line1" required />
            <small class="form-text text-muted"> Street Address</small>
            <div class="invalid-feedback">Please Enter Valid Address </div>
            <input class="form-control mt-3" type="text" id="address_line2" value="<?= esc_attr($warranty->address_line2) ?>" name="address_line2" />
            <small class="form-text text-muted"> Address Line 2</small>
          </div>
          <div class="row m-0 p-0">
            <div class="col-md-6 mb-2">
              <input class="form-control mt-3" type="text" id="city" value="<?= esc_attr($warranty->city) ?>" name="city" required />
              <small class="form-text text-muted"> City</small>
              <div class="invalid-feedback">Please Enter City</div>
            </div>
            <div class="col-md-6 mb-2">
              <input class="form-control mt-3" type="text" id="state" value="<?= esc_attr($warranty->state) ?>" name="state" required />
              <small class="form-text text-muted"> State/Region/Province</small>
              <div class="invalid-feedback">Please Enter State</div>
            </div>
            <div class="col-md-6 mb-2">
              <input class="form-control mt-3" type="text" id="zip" name="zip" value="<?= esc_attr($warranty->zip) ?>" required />
              <small class="form-text text-muted"> Postal / Zip Code</small>
              <div class="invalid-feedback">Please Enter Zip Code</div>
            </div>
          </div>
          <!-- Phone -->
          <div class="mb-3">
            <label for="customer_phone" class="form-label mt-2">Customer Contact Phone <span
                class="text-danger">*</span></label>
            <input class="form-control" type="tel" name="customer_phone" value="<?= esc_attr($warranty->phone) ?>" id="customer_phone" required maxlength="10" />
            <div class="invalid-feedback">Please Enter Valid Contact Number</div>
          </div>
          <!-- Email -->
          <div class="mb-3">
            <label class="form-label" for="customer_email">Customer Email <span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="customer_email" value="<?= esc_attr($warranty->email) ?>" name="customer_email" required />
            <div class="invalid-feedback">Please enter a valid Email</div>
          </div>
          <!-- Room -->
          <div class="mb-3">
            <label class="form-label">Where in your home/business will the installation take place? <span
                class="text-danger">*</span></label>
            <select class="form-select select-with-other" id="room" name="room" required>
              <option value="<?= esc_attr($warranty->room) ?>"><?= esc_attr($warranty->room) ?></option>
              <option value="Kitchen">Kitchen</option>
              <option value="Bathroom">Bathroom</option>
              <option value="Other"> Other</option>
            </select>
            <div class="other-wrapper mt-2" style="display:none;">
            <input class="form-control" type="text" id="other_room" placeholder="Please Enter Here" name="other_room" />
        </div>
            <div class="invalid-feedback">Please Select which room is the damaged</div>
          </div>
          <!-- Installation Date -->
          <div class="mb-3">
            <label class="form-label">Estimated Installation Date <span class="text-danger">*</span></label>
            <input type="text" id="install_date" name="install_date" class="form-control" value="<?= esc_attr($warranty->install_date) ?>" min="<?php echo date('Y-m-d'); ?>" required />
            <div class="invalid-feedback">Please enter a Countertop Installation Date</div>
          </div>
          <div>
          </div>
          <div class="mt-2  text-center">
            <div class="text-center">
              <button type="submit" name="update_warranty" class="btn btn-primary mt-3 form_submit_btn">Save Changes</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<?php get_footer(); ?>
<script>
$(function(){
    $("#install_date").datepicker({
    dateFormat: "mm-dd-yy",
    minDate: 0,
    changeMonth: true,
    changeYear: true
});
});
</script>
