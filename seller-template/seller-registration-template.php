<?php
/**
 * Template Name: Seller Registration Template
 */
function uim_enqueue_admin_assets()
{
  $plugin_url = plugin_dir_url(__DIR__);
  wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
  wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
  wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
  wp_enqueue_script('warrant-form', $plugin_url . 'js/custom.js', array('jquery'), null, true);
  wp_enqueue_script('signature_pad',  $plugin_url . 'js/signature_pad.umd.min.js' , array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');
get_header();
$error="";
$success="";
$plugin_url = plugin_dir_url(__DIR__);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;
    $table = 'become_authorized_retailer';
    $onboarding_email = sanitize_email($_POST['on-boarding_email']);
    $first_name = sanitize_text_field($_POST['customer_first_name']);
    
    $business=sanitize_text_field($_POST['business']);
    $otherBusiness = !empty($_POST['other_select']) ? sanitize_text_field($_POST['other_select']) : '';
    $finalBusiness = ($business === "Other" && !empty($otherBusiness)) ? $otherBusiness : $business;
    
    $email_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE onboarding_contact_email = %s",
            $onboarding_email
        )
    );
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $response = wp_remote_post(
        "https://www.google.com/recaptcha/api/siteverify",
        array(
            'body' => array(
                'secret'   => 'YOUR_RECAPTCHA_KEY',
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        )
    );

    $response_body = wp_remote_retrieve_body( $response );
    $result = json_decode( $response_body, true );
   if ( isset($result['success']) && $result['success'] ) {
        if ($email_exists > 0) {
            $error = "❌ This onboarding contact email is already registered.";
        } else {
                    $upload_dir = wp_upload_dir();
                    $upload_path = $upload_dir['basedir'] . '/signatures/';
                    $upload_url  = $upload_dir['baseurl'] . '/signatures/';
                    if (!file_exists($upload_path)) {
                        mkdir($upload_path, 0755, true);
                    }
                    $data = $_POST['signature_data'];
                    $data = str_replace('data:image/png;base64,', '', $data);
                    $data = str_replace(' ', '+', $data);
                    $imageData = base64_decode($data);
                    $fileName = 'signature_' . time() . '.png';
                    file_put_contents($upload_path . $fileName, $imageData);
                    $signature_url = $upload_url . $fileName;
            $inserted = $wpdb->insert($table, [
                'business_name' => sanitize_text_field($_POST['buisiness-name']),
                'business_owner_first' => $first_name,
                'business_owner_last' => sanitize_text_field($_POST['customer_last_name']),
                'address_street' => sanitize_text_field($_POST['address_line1']),
                'address_city' => sanitize_text_field($_POST['city']),
                'address_state' => sanitize_text_field($_POST['state']),
                'address_postal' => sanitize_text_field($_POST['zip']),
                'phone_number' => sanitize_text_field($_POST['customer_phone']),
                'address_country' => sanitize_text_field($_POST['country-select']),
                'business_owner_email' => sanitize_email($_POST['customer_email']),
                'onboarding_contact_first' => sanitize_text_field($_POST['on-boarding_first_name']),
                'onboarding_contact_last' => sanitize_text_field($_POST['on-boarding_last_name']),
                'onboarding_contact_phone' => sanitize_text_field($_POST['on-boarding_phone']),
                'onboarding_contact_email' => $onboarding_email,
                'type_of_business' =>$finalBusiness,
                'number_of_monthly_installs' => sanitize_text_field($_POST['plan_number']),
                'account_billing_email' => sanitize_email($_POST['account_email']),
                'products_selected' => $products,
                'signature' => $signature_url,
                'message' => sanitize_text_field($_POST['description']),
                'submitted_at' => current_time('mysql'),
            ]);
            if ($inserted) {
        $success = "✅ Form submitted successfully!";
        $website_name  = get_bloginfo('name');
        $support_email = get_option('admin_email');
        $subject = 'Welcome! Thank You for Becoming an Elite Warranty Reseller';
    
        // Build HTML email body
        $body = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>Welcome to the Elite Warranty Reseller Program!</title>
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
                    <p>Dear <strong>' . esc_html(ucwords($_POST['customer_first_name'])) . '</strong>,</p>
    
                    <p>Thank you for requesting with <strong>Elite Surface Protection</strong>. 
                    We have successfully received your details, and your account is now <strong>pending approval</strong>.</p>
    
                    <p>Once your account is approved, you will receive an email with your login instructions 
                    and access to all member features.</p>
    
                    <p>If you have any questions in the meantime, feel free to contact us at 
                    <a href="mailto:' . esc_html($support_email) . '" style="color:#2a4875;text-decoration:none;">' . esc_html($support_email) . '</a>.</p>
    
                    <p>We appreciate your interest in joining our community and look forward to having you on board!</p>
    
                    <p style="margin-top:25px;">Thank you,<br>
                    <strong>The Elite Warranty Program Team</strong></p>
                </div>
                
                <!-- Footer -->
                <div style="background:#f3f3f3;text-align:center;padding:15px;font-size:13px;color:#767676;">
                    <a href="https://elitewarrantyprogram.com/" style="color:#2a4875;text-decoration:none;">
                        www.elitewarrantyprogram.com
                    </a>
                </div>
            </div>
        </body>
        </html>';
    
        // Email headers
        $headers = array(
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Elite Surface Protection <customer-support@elitewarrantyprogram.com>'
        );
    
        // Send email
        $mail_sent = wp_mail($onboarding_email, $subject, $body, $headers);
        
        if (!$mail_sent) {
            error_log("Email to {$onboarding_email} failed to send.");
        }
        } else {
            $error = "❌ There was an error submitting the form. Please try again.";
        }
        
        $admin_email = get_option('admin_email');
        $subject_admin = 'New Reseller Registration Submitted – Elite Warranty Program';
    
        // Build HTML email body for admin
        $body_admin = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8" />
                <title>New Reseller Registration</title>
            </head>
            <body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background-color:#f9f9f9;">
                <div style="max-width:600px;margin:auto;border:1px solid #2a4875;border-radius:10px;overflow:hidden;background:#ffffff;">
                    
                    <!-- Header -->
                    <div style="text-align:center;background:#2a4875;padding:15px;">
                        <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/logo1-white.png" height="80" 
                             alt="Elite Warranty Logo" 
                             style="display:block;margin:0 auto;max-width:200px;height:auto;">
                    </div>
                    
                    <!-- Body Content -->
                    <div style="padding:20px;font-size:15px;color:#333;line-height:1.6;">
                        <p>Dear Admin,</p>
                        <p>A new reseller request form has been submitted on <strong>' . esc_html(get_bloginfo('name')) . '</strong>.</p>
            
                        <p><strong>Submitted Details:</strong></p>
                        <ul>
                            <li><strong>Business Owner:</strong> ' . esc_html(ucwords($_POST['customer_first_name'] . ' ' . $_POST['business_owner_last'])) . '</li>
                            <li><strong>Email:</strong> ' . esc_html($onboarding_email) . '</li>
                            <li><strong>Phone:</strong> ' . esc_html($_POST['customer_phone']) . '</li>
                            <li><strong>Business Name:</strong> ' . esc_html($_POST['business_name']) . '</li>
                        </ul>
            
                        <p>You can review and approve this reseller in the admin dashboard.</p>
                    </div>
                    
                    <!-- Footer -->
                    <div style="background:#f3f3f3;text-align:center;padding:15px;font-size:13px;color:#767676;">
                        <a href="https://elitewarrantyprogram.com/wp-admin" style="color:#2a4875;text-decoration:none;">
                            Go to Admin Dashboard
                        </a>
                    </div>
                </div>
            </body>
            </html>';
            
            // Email headers
            $headers_admin = array(
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: Elite Surface Protection <customer-support@elitewarrantyprogram.com>'
            );
            
            // Send admin email
            wp_mail($admin_email, $subject_admin, $body_admin, $headers_admin);
    
        
        }
   } else {
        $error = "❌ reCAPTCHA verification failed. Please try again.";
    }
}
?>

<style>
   .entry-hero {
    visibility: visible;
    height: auto;
}
</style>
<section role="banner" class="entry-hero page-hero-section entry-hero-layout-standard">
  <div class="entry-hero-container-inner">
    <div class="hero-section-overlay"></div>
    <div class="hero-container site-container">
      <header class="entry-header page-title title-align-inherit title-tablet-align-inherit title-mobile-align-inherit">
        <h1 class="entry-title"><?php echo the_title(); ?></h1>
        <nav id="code4rest-breadcrumbs" aria-label="Breadcrumbs" class="code4rest-breadcrumbs">
          <div class="code4rest-breadcrumb-container">
            <span>
              <a href="https://elitewarrantyprogram.idestpro.com/" title="Home" itemprop="url"
                class="code4rest-bc-home code4rest-bc-home-icon"><span><span class="code4rest-svg-iconset svg-baseline">
                    <svg aria-hidden="true" class="code4rest-svg-icon code4rest-home-svg" fill="currentColor"
                      version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                      <title>Home</title>
                      <path d="M9.984 20.016h-4.969v-8.016h-3l9.984-9 9.984 9h-3v8.016h-4.969v-6h-4.031v6z"></path>
                      </svg>
                          </a>
                      </span>
                      <span class="bc-delimiter">/</span>
                       <span><a href="/for-installers/" itemprop="url"><span>For Installers</span></a></span>
                       <span class="bc-delimiter">/</span> 
                       <span class="code4rest-bread-current"><?php echo the_title(); ?></span>
          </div>
        </nav>
      </header>
    </div>
  </div>
</section>
<div class="container mt-5 mb-5 file-a-claim container-width">
    <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
    <?php endwhile; ?>
<?php endif; ?>
<?php if (!empty($error)) : ?>
<div class="alert alert-danger" role="alert">
  <?php echo  $error ?>
</div>
<?php endif; ?>
<?php if (!empty($success)) : ?>
<div class="alert alert-success" role="alert">
 <?php echo  $success ?>
</div>
<?php endif; ?>
  <form method="post" id="warranty-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"
    enctype="multipart/form-data" onsubmit="saveSignature()" class="form-validation form_bg" novalidate >
    <div class="row">
      <div class="col-12 valid  mb-3">
        <label for="buisiness-name" class="form-label">Business Name <span class="text-danger">*</span></label>
        <input class="form-control" type="text" id="buisiness-name" name="buisiness-name" required />
        <div class="invalid-feedback">Please Enter Business Name</div>
      </div>
      <div class="col-12 mb-12">
        <label for="customer_first_name" class="form-label">Business Owner <span class="text-danger">*</span></label>
      </div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="customer_first_name" name="customer_first_name" required />
        <div class="invalid-feedback">Please Enter Your First Name</div>
        <small class="form-text text-muted">First</small>
      </div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="customer_last_name" name="customer_last_name" required />
        <div class="invalid-feedback">Please Enter Your Last Name</div>
        <small class="form-text text-muted">Last</small>
      </div>
      <!-- Email -->
      <div class="mb-3 mt-2">
        <label class="form-label" for="customer_email">Business Owner Email <span class="text-danger">*</span></label>
        <input class="form-control" type="email" id="customer_email" name="customer_email" required />
        <div class="invalid-feedback">Please enter a valid Email</div>
        <small class="form-text text-muted">example@example.com</small>
      </div>
      <!-- Phone -->
      <div class="mb-3">
        <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
        <input class="form-control" type="tel" name="customer_phone" id="customer_phone" required maxlength="10" />
        <div class="invalid-feedback">Please Enter Valid Contact Number</div>
      </div>
      <div class="col-12">
        <label class="form-label" for="address_line1">Business Address <span class="text-danger">*</span></label>
        <input class="form-control" type="text" id="address_line1" name="address_line1" required />
        <div class="invalid-feedback">Please Enter Valid Address </div>
        <small class="form-text text-muted"> Street Address</small>
      </div>
      <div class="row m-0 p-0">
        <div class="col-md-6 mb-2">
          <input class="form-control mt-3" type="text" id="city" name="city" required />
          <div class="invalid-feedback">Please Enter City</div>
          <small class="form-text text-muted"> City</small>
        </div>
        <div class="col-md-6 mb-2">
          <input class="form-control mt-3" type="text" id="state" name="state" required />
           <small class="form-text text-muted"> State/Region/Province</small>
          <div class="invalid-feedback">Please Enter State</div>
        </div>
        <div class="col-md-6 mb-2">
          <input class="form-control mt-3" type="text" id="zip" name="zip"  required />
          <div class="invalid-feedback">Please Enter Zip Code</div>
          <small class="form-text text-muted"> Postal / Zip Code</small>
        </div>
        <div class="col-md-6 mb-2 mt-3 select_country">
            <?php
                global $wpdb;
                $countriestable ='geo_countries';
                $countries = $wpdb->get_results("SELECT country_id, country_name FROM {$countriestable}");
                ?>
               <select name="country-select" id="country-select" class="form-control" style="width:100%">
                  <option value="">-Select Country-</option>
               </select>
               <small class="form-text text-muted">Country</small>
        </div>
      </div>
       <div class="col-12 mb-12">
        <label  class="form-label">On-Boarding Contact <span class="text-danger">*</span></label>
      </div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="on-boarding_first_name" name="on-boarding_first_name" required />
        <div class="invalid-feedback">Please Enter Your First Name</div>
        <small class="form-text text-muted">First</small>
      </div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="on-boarding_last_name" name="on-boarding_last_name" required />
        <div class="invalid-feedback">Please Enter Your Last Name</div>
        <small class="form-text text-muted">Last</small>
      </div> 
    <!-- Phone -->
      <div class="mb-3">
        <label for="on-boarding_phone" class="form-label mt-2">On-Boarding Contact Phone <span class="text-danger">*</span></label>
        <input class="form-control" type="tel" name="on-boarding_phone" id="on-boarding_phone" required maxlength="10" />
        <div class="invalid-feedback">Please Enter Valid Contact Number</div>
      </div>
      <!-- Email -->
      <div class="mb-3">
        <label class="form-label" for="on-boarding_email">On-Boarding Contact Email  <span class="text-danger">*</span></label>
        <input class="form-control" type="email" id="on-boarding_email" name="on-boarding_email" required />
        <div class="invalid-feedback">Please enter a valid Email</div>
        <small class="form-text text-muted">example@example.com</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Type of Business <span
            class="text-danger">*</span></label>
        <select class="form-select select-with-other" id="business" name="business" required>
          <option value="">-Select-</option>
          <option value="Stone Fabricator">Stone Fabricator</option>
          <option value="Contractor">Contractor</option>
          <option value="Kitchen & Bath">Kitchen & Bath</option>
          <option value="Builder">Builder</option>
          <option value="Other">Other (Please Specify Below)</option>
        </select>
        <div class="other-wrapper mt-2" style="display:none;">
            <input class="form-control" type="text" id="other_select" placeholder="Type Here Business Name" name="other_select" />
        </div>
        <div class="invalid-feedback">Please Select Business</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="account_email">Account Billing Email  <span class="text-danger">*</span></label>
        <input class="form-control" type="email" id="account_email" name="account_email" required />
        <div class="invalid-feedback">Please enter a valid Email</div>
        <small class="form-text text-muted">example@example.com</small>
      </div>
      <!-- What happened -->
      <div class="mb-3">
        <label class="form-label">Message </label>
        <textarea class="form-control" name="description" rows="3"></textarea>
      </div>

         <div class="mb-3">
              <label class="form-label">Signature </label>
             <canvas id="signature-pad"></canvas>
             <input type="hidden" name="signature_data" id="signature_data">
             <span id="siClearBtn" onclick="clearSignature()">Clear</span>
         </div> 
              <div class="mb-3">
      <div class="form-check user-check">
              <input class="form-check-input" type="checkbox" value="" id="granitegold" required >
               <label class="form-check-label" for="granitegold">
               I agree to the <a href="<?php echo site_url()?>/terms-and-conditions/ ">terms & conditions</a> <span
            class="text-danger">*</span></label>
                </label>
          </div>
          <div class="invalid-feedback">Please check terms & conditions</div>
          </div>
          <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_KEY"></div>
         <div class="text-center">
          <button type="submit" class="btn btn-primary mt-3 form_submit_btn">Submit</button>
          </div>
      </div>
    </div>
  </form>
  <!-- Load reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</div>
<?php get_footer(); ?>