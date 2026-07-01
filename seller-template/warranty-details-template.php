<?php
/**
 * Template Name: Warranty Details template
 */
function uim_enqueue_admin_assets()
{
  $plugin_url = plugin_dir_url(__DIR__);
  wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
  wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
  wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
  wp_enqueue_script('bootstrap-bundle', $plugin_url . '/js/bootstrap.bundle.min.js', array('jquery'), null, true);
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
$paymentstatus = $wpdb->prefix . "warranty_payments";
$massage = [];
$error = [];
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
?>
<style>
  .margin-custom {
    margin-top: 140px;
  }

  .details_box {
    background: #fff;
  }

  .details_box .table li,
  .details_box .table li {
    padding: 20px 20px;
    font-size: 18px;
  }

  .btn {
    widli: auto;
  }
</style>
<div class="container margin-custom mb-5">
  <!-- <h1 class="text-center mb-5">Protection Plan Claim Form</h1> -->
  <div class="row justify-content-center elite-custom-account">
    <div class="col-md-3 col-lg-3 ">
      <?php sidebar() ?>
    </div>
    <div class="col-md-9 col-lg-9 form_bg">
      <div class="elite-custom-top mb-4">
        <h1>Warranty Details</h1>
      </div>
      <div class="details_box">
          <form>
        <ul>
            <li>
              <h5>Purchase Date</h5>
              <p><?php echo date("m/d/Y", strtotime($warranty->submitted_at)); ?></p>
            </li>
            <li>
              <h5>Warranty Number</h5>
              <p><?= esc_attr ($warranty_id);  ?></p>
            </li>
            <li>
              <h5>Protection Plan type</h5>
              <p><?= esc_attr($warranty->plan_name) ?> ($<?= number_format(esc_attr($warranty->price), 2) ?>)</p>
            </li>
            <li>
              <h5>Name</h5>
              <p style="text-transform: capitalize;"><?= esc_attr($warranty->first_name) ?> <?= esc_attr($warranty->last_name) ?></p>
            </li>
            <li>
              <h5>Address</h5>
              <p><?= esc_attr($warranty->address_line1) ?>,
                <?= $warranty->address_line2 ? esc_attr($warranty->address_line2) . ',' : '' ?>
                <?= esc_attr($warranty->city) ?>, <?= esc_attr($warranty->state) ?>, <?= esc_attr($warranty->zip) ?>
              </p>
            </li>
            <li>
              <h5>Phone Number</h5>
              <p><?= esc_attr($warranty->phone) ?></p>
            </li>
            <li>
              <h5>Email</h5>
              <p> <?= esc_attr($warranty->email) ?></p>
            </li>
            <li>
              <h5>Estimated Countertop Date</h5>
              <p><?= esc_attr($warranty->install_date) ?></p>
            </li>
            <li>
              <h5>Price</h5>
              <p>$<?= number_format(esc_attr($warranty->price), 2) ?></p>
            </li>

        </ul>

        <div class="d-flex justify-content-between mt-5 align-items-center">
          <?php 
         wp_cache_flush();
          $payment = $wpdb->get_results(
              $wpdb->prepare("SELECT * FROM $paymentstatus WHERE warranty_id = %d", $warranty->id)
              );
               $latest_payment = $payment[ array_key_last( $payment ) ];
             if ($latest_payment->status == '' || $latest_payment->status === 'pending' || $latest_payment->status === 'cancelled'){ 
              ?>
             <a class="btn btn-secondary"
            href="<?php echo esc_url(add_query_arg('warranty_id', $warranty->id, site_url('/edit-warranty/'))); ?>">Back</a>
            <?php 
             }
            if (($latest_payment->status) == 'paid') {
              echo '<div class="alert w-100 alert-success" role="alert">
                     Payment Completed
                    </div>';
          } 
          else{
          ?>
            <a class="btn btn-primary" href="<?php echo esc_url( add_query_arg('pay_warranty',$warranty->id, site_url('/process-payment/'))); ?>">
            Pay Now
          </a>
          <?php } ?>
        </div>
       </form>
      </div>
    </div>
  </div>
</div>

</div>
<?php get_footer(); ?>