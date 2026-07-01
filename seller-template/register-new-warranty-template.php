<?php
  /**
   * Template Name: Register New Warranty Template
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

<style>
  .elite-custom-form {
    margin: 8.7em 0;
  }

  .elite-custom-form Input,
  .elite-custom-form select,
  .elite-custom-form textarea,
  .elite-custom-form input[type=radio] {
    width: 100%;
    max-width: 100%;
    padding: 12px;
    height: auto;
    background: transparent !important;
    border: 1px solid #2a4875;
    color: #000;
  }

  .elite-custom-form input,
  .elite-custom-form input::placeholder {
    background-color: Transparent !important;
    color: #000;

  }

  .elite-custom-form a {
    color: #2a4875;
    text-decoration: none;
  }

  .elite-custom-top h3 {
    font-size: 28px;
    font-weight: 700;
    line-height: 44px;
    color: #000;
    text-align: center;
    text-transform: capitalize;
  }

  .elite-custom-top p {
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
    color: #000;
    text-align: center;
  }

  .form-check-input:checked[type=checkbox] {
    background-color: #2a4875 !important;
  }
  .elite-custom-account li:hover a,

  .elite-custom-account li:active a {

    background: #4b6c9d;

    color: #2a4875;

    border-radius: 0px;

  }



  .elite-custom-account li a.active {

    background: #4b6c9d !important;

    color: #fff !important;

    border-radius: 0px;

  }



  .elite-custom-account li a svg,.elite-custom-account li a svg path {
    width: 26px;
    height: 26px; fill: #fff;
    margin-right: 8px;

  }

  .elite-custom-account li:hover a {
     color: #fff;

  }

  .elite-custom-account li a {
    color: #fff;
    text-decoration: none;

  }

  .elite-custom-account .edit-profile {
    box-shadow: 0 0 5px;
    padding: 30px;

  }
    .sidebar {

    background-color: #2a4875;

    height: 100vh;

  }
  ul.nav-pills {
 margin: 0 0 10px;

  }
  ul.nav-pills  li a{
       padding: 25px 18px;
  }

  ul.nav-pills .nav-link.active,.nav-pills .show>.nav-link {
    color: #fff;background-color: #2a4875;

  }

  ul.nav-pills .nav-link {color: #fff; }
</style>


<section role="banner" class="entry-hero page-hero-section entry-hero-layout-standard">
  <div class="entry-hero-container-inner">
    <div class="hero-section-overlay"></div>
    <div class="hero-container site-container">
      <header class="entry-header page-title title-align-inherit title-tablet-align-inherit title-mobile-align-inherit">
        <h1 class="entry-title"><?php echo the_title() ?></h1>
        <nav id="code4rest-breadcrumbs" aria-label="Breadcrumbs" class="code4rest-breadcrumbs">
          <div class="code4rest-breadcrumb-container">
            <span>
              <a href="https://elitewarrantyprogram.idestpro.com/" title="Home" itemprop="url"
                class="code4rest-bc-home code4rest-bc-home-icon">
                <span>
                  <span class="code4rest-svg-iconset svg-baseline">
                    <svg aria-hidden="true" class="code4rest-svg-icon code4rest-home-svg" fill="currentColor"
                      version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                      <title>Home</title>
                      <path d="M9.984 20.016h-4.969v-8.016h-3l9.984-9 9.984 9h-3v8.016h-4.969v-6h-4.031v6z"></path>
                    </svg>
                  </span>
                </span>
              </a>
            </span>
            <span class="bc-delimiter">/</span>
            <span class="code4rest-bread-current"><?php echo the_title() ?></span>
          </div>
        </nav>
      </header>
    </div>
  </div>
</section>

<?php
$plugin_url = plugin_dir_url(__DIR__);
if (!is_user_logged_in()) {
       wp_redirect(site_url('/retailer-login/')); 
      exit;
  }
  
  $user_id = get_current_user_id();
  $user = get_userdata($user_id);
  if (!empty($_POST['install_date'])) {
    $install_date = $_POST['install_date'];
    $today = date('Y-m-d');
    
    if ($install_date < $today) {
        $errors[] = "Please select today or a future date.";
    }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_warranty'])) {
    global $wpdb;
    $table = 'seller_purchaser_info';
    $massage= [];
    $error=[];
    
      $email     = sanitize_email($_POST['customer_email']);
      $plan      = sanitize_text_field($_POST['plan_type']);
      $install   = sanitize_text_field($_POST['install_date']);
      $fname     = sanitize_text_field($_POST['customer_first_name']);
     $lname     = sanitize_text_field($_POST['customer_last_name']);
    $phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $room=sanitize_text_field($_POST['room']);
    $otherroom = !empty($_POST['other_room']) ? sanitize_text_field($_POST['other_room']) : '';
    $finalroom = ($room === "Other" && !empty($otherroom )) ? $otherroom  : $room;
    
         if(!isset($_POST['_warranty_nonce']) || !wp_verify_nonce($_POST['_warranty_nonce'],'add_seller_warranty')){
             $error[]='invalid submision';
         }
    else{
    $inserted = $wpdb->insert($table, [
        'status'                  => sanitize_text_field($_POST['status']),  
        'reseller_id'            =>$user_id,
        'plan_type'              => $plan,
        'first_name'             =>  $fname,
        'last_name'              => $lname,
        'address_line1'          => sanitize_text_field($_POST['address_line1']),
        'address_line2'          => sanitize_text_field($_POST['address_line2']),
        'city'                   => sanitize_text_field($_POST['city']),
        'state'                  => sanitize_text_field($_POST['state']),
        'zip'                    => sanitize_text_field($_POST['zip']),
        'phone'                  => $phone,
        'email'                  => $email,
        'room'                   => $finalroom,
        'install_date'           => $install,
        'submitted_at'           => current_time('mysql'),
    ]);

    // Show feedback
    if (!$inserted) {
        $error[] = 'DB Error' . $wpdb->last_error;
    } else {
     $plan_type = $wpdb->get_var( $wpdb->prepare("SELECT name FROM `warranty_plans` WHERE id = %d",$plan));
       $massage[]="Warranty Added";
       $insert_id = $wpdb->insert_id; 
       $subject = 'Your Warranty Submission Received';
            $body    = '
                <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>Your Warranty Submission Received</title>
        </head>
        <body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background-color:#f9f9f9;">
            <div style="max-width:600px;margin:auto;border:1px solid #2a4875;border-radius:10px;overflow:hidden;background:#ffffff;">
                
                <!-- Logo Section -->
                <div style="text-align:center;background:#2a4875;padding:15px;">
                    <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/logo1-white.png" height="80" 
                         alt="Elite Warranty Logo" 
                         style="display:block;margin:0 auto;max-width:200px;height:auto;">
                </div>
                
                <!-- Body Content -->
                <div style="padding:20px;font-size:15px;color:#333;line-height:1.6;">
                    <p>Hi ' . esc_html(ucwords($fname)) . ',</p>
                    <p>Warranty Number :'.$insert_id.' </p>
                    <p>Plan Name :'.$plan_type.' </p>
                     <p>Thank you for registering your warranty! We have received the details and will process it shortly.</p>
                   <p>If you have questions, contact us anytime.</p>
                   <p>Thank you,<br>The Support Team</p>
                </div>
                
                <!-- Footer -->
                <div style="background:#f3f3f3;text-align:center;padding:15px;font-size:13px;color:#767676;">
                    <a mailto="customer-support@elitewarrantyprogram.com" style="color:#2a4875;text-decoration:none;">
                        customer-support@elitewarrantyprogram.com
                    </a>
                </div>
            </div>
        </body>
        </html>';

           $headers = array(
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Elite Surface Protection <customer-support@elitewarrantyprogram.com>'
        );
            add_filter('wp_mail_content_type', function($c){ return 'text/html'; });

            $mail_sent = wp_mail($email, $subject, $body, $headers);

            remove_filter('wp_mail_content_type', '__return_html');

            if (!$mail_sent) {
                error_log("wp_mail failed to send to {$email}");
            }
            
            // Admin Notification
            $admin_email = get_option('admin_email');
            $admin_subject = 'New Warranty Submitted';
            $admin_body    = '
                <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>Your Warranty Submission Received</title>
        </head>
        <body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background-color:#f9f9f9;">
            <div style="max-width:600px;margin:auto;border:1px solid #2a4875;border-radius:10px;overflow:hidden;background:#ffffff;">
                
                <!-- Logo Section -->
                <div style="text-align:center;background:#2a4875;padding:15px;">
                    <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/logo1-white.png" height="80" 
                         alt="Elite Warranty Logo" 
                         style="display:block;margin:0 auto;max-width:200px;height:auto;">
                </div>
                
                <!-- Body Content -->
                <div style="padding:20px;font-size:15px;color:#333;line-height:1.6;">
                     <p style="color:#000;">A new warranty has been submitted with the following details:</p>
                      <ul style="padding-left:0;">
                      <li><strong>Warranty Number :</strong>'.$insert_id.' </li>
                    <li><strong>Plan Name :'.$plan_type.' </li>
                      <li><strong>Name:</strong> ' . esc_html($fname . ' ' . $lname) . '</li>
                     <li><strong>Install Date:</strong> ' . esc_html($install) . '</li>
                     <li><strong>Email:</strong> ' . esc_html($email) . '</li>
                     <li><strong>Contact Number:</strong> ' . esc_html($phone) . '</li>
                </ul>
                </div>
                
                <!-- Footer -->
                <div style="background:#f3f3f3;text-align:center;padding:15px;font-size:13px;color:#767676;">
                    <a mailto="customer-support@elitewarrantyprogram.com" style="color:#2a4875;text-decoration:none;">
                        customer-support@elitewarrantyprogram.com
                    </a>
                </div>
            </div>
        </body>
        </html>';

            wp_mail($admin_email, $admin_subject, $admin_body, $headers);

           add_filter('wp_mail_content_type', function($c){ return 'text/html'; });
            
            wp_redirect(add_query_arg(['warranty_id' => $insert_id], site_url('/warranty-details')));
        exit;
    }
    }
    
}

?>
<div class="container mt-5 mb-5">
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
    
    <div class="col-md-3 col-lg-3">
      <?php sidebar() ?>
    </div>
    <div class="col-md-9 col-lg-9 form_bg">
           <div class="elite-custom-top  mb-4">
        <h1>Register New Warranty</h1>
      </div>
     <form method="post" id="warranty-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"
    enctype="multipart/form-data" class="needs-validation" novalidate>
       <?php wp_nonce_field('add_seller_warranty', '_warranty_nonce'); ?> 
    <div class="row">
        <input type="hidden" name="status" value="pending">
        <div class="col-12 valid  mb-3">
            <label for="plan_type" class="form-label">Select your Protection Plan type.<span
                class="text-danger">*</span></label>
            <?php 
            global $wpdb;
            $plantable = 'warranty_plans';
              $warrantyplans = $wpdb->get_results("SELECT * FROM $plantable");
            ?>    
            <select class="form-select" id="plan_type" name="plan_type" required>
              <option value="">-Select-</option>
              <?php  foreach ($warrantyplans as $warrantyplan) { ?>
              <option value="<?php echo  esc_attr($warrantyplan->id) ?>"><?php echo  esc_attr ($warrantyplan->name)?></option>
              <?php } ?>
            </select>
            <div class="invalid-feedback">Please Select your Protection Plan type</div>
          </div>
         <div class="col-12 mb-12"> 
            <label for="customer_first_name" class="form-label">Customer Name<span
                class="text-danger">*</span></label>
          </div>
          <div class="col-6 mb-6">
            <input class="form-control" type="text" id="customer_first_name" name="customer_first_name" required />
            <small class="form-text text-muted">First</small>
            <div class="invalid-feedback">Please Enter Your First Name</div>
          </div>
          <div class="col-6 mb-6">
            <input class="form-control" type="text" id="customer_last_name" name="customer_last_name" required />
            <small class="form-text text-muted">Last</small>
            <div class="invalid-feedback">Please Enter Your First Name</div>
          </div>
      <div class="col-12 mt-3">
            <label class="form-label" for="address_line1">Customer Address <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="address_line1" name="address_line1" required />
            <small class="form-text text-muted"> Street Address</small>
            <div class="invalid-feedback">Please Enter Valid Address </div>
            <input class="form-control mt-3" type="text" id="address_line2" name="address_line2" />
            <small class="form-text text-muted"> Address Line 2</small>
          </div>
          <div class="row m-0 p-0">
            <div class="col-md-6 mb-2">
              <input class="form-control mt-3" type="text" id="city" name="city" required />
              <small class="form-text text-muted"> City</small>
              <div class="invalid-feedback">Please Enter City</div>
            </div>
            <div class="col-md-6 mb-2">
              <input class="form-control mt-3" type="text" id="state" name="state" required />
              <small class="form-text text-muted"> State/Region/Province</small>
              <div class="invalid-feedback">Please Enter State</div>
            </div>
            <div class="col-md-6 mb-2">
              <input class="form-control mt-3" type="text" id="zip" name="zip" required />
              <small class="form-text text-muted"> Postal / Zip Code</small>
              <div class="invalid-feedback">Please Enter Zip Code</div>
            </div>
          </div>
      <!-- Phone -->
      <div class="mb-3">
            <label for="customer_phone" class="form-label mt-2">Customer Contact Phone<span
                class="text-danger">*</span></label>
            <input class="form-control" type="tel" name="customer_phone" id="customer_phone" required maxlength="10" />
            <div class="invalid-feedback">Please Enter Valid Contact Number</div>
          </div>

      <!-- Email -->
      <div class="mb-3">
            <label class="form-label" for="customer_email">Customer Email <span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="customer_email" name="customer_email" required />
            <div class="invalid-feedback">Please enter a valid Email</div>
          </div>
      <!-- Room -->
      <div class="mb-3">
            <label class="form-label">Where in your home/business will the installation take place? <span
                class="text-danger">*</span></label>
            <select class="form-select select-with-other" id="room" name="room" required>
              <option value="">-Select-</option>
              <option value="Kitchen">Kitchen</option>
              <option value="Bathroom">Bathroom</option>
              <option value="Other"> Other</option>
            </select>
             <div class="other-wrapper mt-2" style="display:none;">
                <input class="form-control" type="text" id="other_room" placeholder="Please Enter Here" name="other_room" />
            </div>
            <div class="invalid-feedback">Please Select which room is the damaged</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Estimated Installation Date <span class="text-danger">*</span></label>
            <input type="text" id="install_date" name="install_date" class="form-control" placeholder="MM-DD-YY" required>
            <div class="invalid-feedback">Please enter a Countertop Installation Date</div>
          </div>
      <div class="mt-2  text-center">
        <div class="text-center">
        <button type="submit" name="add_warranty" class="btn btn-primary mt-3 form_submit_btn">Submit</button>
        </div>
      </div>
    </div>
  </form>
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
