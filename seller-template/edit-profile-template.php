<?php
  /**
   * Template Name: Edit Profile Template
   */
    function uim_enqueue_admin_assets()
  {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
     wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
  }
  add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');
  get_header();
 ?>
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
<div class="container mt-5 mb-5">
   <?php
if (!is_user_logged_in()) {
    wp_redirect(site_url('/retailer-login/')); 
    exit;
}

global $wpdb;

$user_id = get_current_user_id();
$table   = 'become_authorized_retailer';

$error   = "";
$success = "";

$update_user = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM $table WHERE user_id = %d", 
        $user_id
    )
);

if (!$update_user) {
    wp_die('No retailer profile found for this account.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    // Sanitize inputs
    $onboarding_email = sanitize_email($_POST['on-boarding_email']);
    $first_name       = sanitize_text_field($_POST['customer_first_name']);
    $products         = isset($_POST['authorized_products']) && is_array($_POST['authorized_products']) 
                        ? implode(', ', array_map('sanitize_text_field', $_POST['authorized_products'])) 
                        : '';

    // Default: keep old signature
    $signature_url = $update_user->signature;

    // If a new signature is provided, process it
    if (!empty($_POST['signature_data']) && strpos($_POST['signature_data'], 'data:image/png;base64,') === 0) {
        $upload_dir  = wp_upload_dir();
        $upload_path = trailingslashit($upload_dir['basedir']) . 'signatures/';
        $upload_url  = trailingslashit($upload_dir['baseurl']) . 'signatures/';

        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        $data = base64_decode(str_replace('data:image/png;base64,', '', $_POST['signature_data']));
        if ($data !== false) {
            $fileName = 'signature_' . time() . '.png';
            if (file_put_contents($upload_path . $fileName, $data)) {
                $signature_url = $upload_url . $fileName;
            }
        }
    }

    // Prepare update data (without signature first)
    $update_data = [
        'business_name'              => sanitize_text_field($_POST['buisiness-name']),
        'business_owner_first'       => $first_name,
        'business_owner_last'        => sanitize_text_field($_POST['customer_last_name']),
        'address_street'             => sanitize_text_field($_POST['address_line1']),
        'address_city'               => sanitize_text_field($_POST['city']),
        'address_state'              => sanitize_text_field($_POST['state']),
        'address_postal'             => sanitize_text_field($_POST['zip']),
        'phone_number'               => sanitize_text_field($_POST['customer_phone']),
        'address_country'            => sanitize_text_field($_POST['country-select']),
        'business_owner_email'       => sanitize_email($_POST['customer_email']),
        'onboarding_contact_first'   => sanitize_text_field($_POST['on-boarding_first_name']),
        'onboarding_contact_last'    => sanitize_text_field($_POST['on-boarding_last_name']),
        'onboarding_contact_phone'   => sanitize_text_field($_POST['on-boarding_phone']),
        'onboarding_contact_email'   => $onboarding_email,
        'type_of_business'           => sanitize_text_field($_POST['business']),
        'number_of_monthly_installs' => sanitize_text_field($_POST['plan_number']),
        'account_billing_email'      => sanitize_email($_POST['account_email']),
        'products_selected'          => $products,
        'message'                    => sanitize_text_field($_POST['description']),
        'submitted_at'               => current_time('mysql'),
    ];

    if ($signature_url !== $update_user->signature) {
        $update_data['signature'] = $signature_url;
    }

    $updated = $wpdb->update(
        $table,
        $update_data,
        ['user_id' => $user_id]
    );

    if ($updated !== false) {
        $success      = "✅ Profile updated successfully!";
        $update_user  = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
    } else {
        $error = "❌ No changes made or update failed.";
    }
}

?>

   <h2> Hii
      <?php echo esc_attr($update_user->business_owner_first) ?>
   </h2>
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
   <div class="row justify-content-center elite-custom-account ">
      <div class="col-md-3 col-lg-3 ">
         <?php sidebar() ?>
      </div>
      <div class="col-md-9 col-lg-9 form_bg">
         <div class=" elite-custom-top mb-4">
            <h1>Edit Profile</h1>
         </div>
         <section id="edit-profile" style="margin-bottom:40px;">
            <form method="post" id="warranty-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"
               enctype="multipart/form-data" onsubmit="saveSignature()" class="form-validation" novalidate>
               <?php wp_nonce_field('update_seller_profile'); ?>
               <div class="row">
                  <div class="col-12 valid  mb-3">
                     <label for="buisiness-name" class="form-label">Buisiness Name<span class="text-danger">*</span></label>
                     <input class="form-control" type="text" id="buisiness-name" name="buisiness-name" value="<?php echo esc_attr($update_user->business_name) ?>" required />
                     <div class="invalid-feedback">Please Enter Buisiness Name</div>
                  </div>
                  <div class="col-12 mb-12">
                     <label for="customer_first_name" class="form-label">Business Owner<span
                        class="text-danger">*</span></label>
                  </div>
                  <div class="col-6 mb-6">
                     <input class="form-control" type="text" id="customer_first_name" name="customer_first_name" value="<?php echo esc_attr($update_user->business_owner_first) ?>"  required />
                     <div class="invalid-feedback">Please Enter Your First Name</div>
                     <small class="form-text text-muted">First</small>
                  </div>
                  <div class="col-6 mb-6">
                     <input class="form-control" type="text" id="customer_last_name"  value="<?php echo esc_attr($update_user->business_owner_last) ?>" name="customer_last_name" required />
                     <div class="invalid-feedback">Please Enter Your Last Name</div>
                     <small class="form-text text-muted">Last</small>
                  </div>
                  <!-- Email -->
                  <div class="mb-3">
                     <label class="form-label" for="customer_email">Business Owner Email <span
                        class="text-danger">*</span></label>
                     <input class="form-control" type="email" id="customer_email" name="customer_email" value="<?php echo esc_attr($update_user->business_owner_email) ?>" required />
                     <div class="invalid-feedback">Please enter a valid Email</div>
                     <small class="form-text text-muted">example@example.com</small>
                  </div>
                  <!-- Phone -->
                  <div class="mb-3">
                     <label for="customer_phone" class="form-label mt-2">Phone Number <span
                        class="text-danger">*</span></label>
                     <input class="form-control" type="tel" name="customer_phone" id="customer_phone" value="<?php echo esc_attr($update_user->phone_number) ?>" required
                        maxlength="10" />
                     <div class="invalid-feedback">Please Enter Valid Contact Number</div>
                  </div>
                  <div class="col-12 mt-3">
                     <label class="form-label" for="address_line1">Business Address <span class="text-danger">*</span></label>
                     <input class="form-control" type="text" id="address_line1" name="address_line1" value="<?php echo esc_attr($update_user->address_street) ?>" required />
                     <div class="invalid-feedback">Please Enter Valid Address </div>
                     <small class="form-text text-muted"> Street Address</small>
                  </div>
                  <div class="row m-0 p-0">
                     <div class="col-md-6 mb-2">
                        <input class="form-control mt-3" type="text" id="city" name="city"  value="<?php echo esc_attr($update_user->address_city) ?>" required />
                        <div class="invalid-feedback">Please Enter City</div>
                        <small class="form-text text-muted"> City</small>
                     </div>
                     <div class="col-md-6 mb-2">
                        <input class="form-control mt-3" type="text" id="state" name="state" value="<?php echo esc_attr($update_user->address_state) ?>" required />
                        <small class="form-text text-muted"> State/Region/Province</small>
                        <div class="invalid-feedback">Please Enter State</div>
                     </div>
                     <div class="col-md-6 mb-2">
                        <input class="form-control mt-3" type="text" id="zip" name="zip" value="<?php echo esc_attr($update_user->address_postal) ?>" placeholder="Postal / Zip Code"
                           required />
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
                           <option value="<?php echo esc_attr($update_user->address_country) ?>"><?php echo esc_attr($update_user->address_country) ?></option>
                        </select>
                        <small class="form-text text-muted">Country</small>
                     </div>
                  </div>
                  <div class="col-12 mb-12">
                     <label class="form-label">On-Boarding Contact<span class="text-danger">*</span></label>
                  </div>
                  <div class="col-6 mb-6">
                     <input class="form-control" type="text" id="on-boarding_first_name" value="<?php echo esc_attr($update_user->onboarding_contact_first) ?>" name="on-boarding_first_name"
                        required />
                     <div class="invalid-feedback">Please Enter Your First Name</div>
                     <small class="form-text text-muted">First</small>
                  </div>
                  <div class="col-6 mb-6">
                     <input class="form-control" type="text" id="on-boarding_last_name" value="<?php echo esc_attr($update_user->onboarding_contact_last) ?>" name="on-boarding_last_name"
                        required />
                     <div class="invalid-feedback">Please Enter Your Last Name</div>
                     <small class="form-text text-muted">Last</small>
                  </div>
                  <!-- Phone -->
                  <div class="mb-3">
                     <label for="on-boarding_phone" class="form-label mt-2">On-Boarding Contact Phone <span
                        class="text-danger">*</span></label>
                     <input class="form-control" type="tel" name="on-boarding_phone" value="<?php echo esc_attr($update_user->onboarding_contact_phone) ?>" id="on-boarding_phone" required
                        maxlength="10" />
                     <div class="invalid-feedback">Please Enter Valid Contact Number</div>
                  </div>
                  <!-- Email -->
                  <div class="mb-3">
                     <label class="form-label" for="on-boarding_email">On-Boarding Contact Email <span
                        class="text-danger">*</span></label>
                     <input class="form-control" type="email" id="on-boarding_email" value="<?php echo esc_attr($update_user->onboarding_contact_email) ?>" name="on-boarding_email" required readonly />
                     <div class="invalid-feedback">Please enter a valid Email</div>
                     <small class="form-text text-muted">example@example.com</small>
                  </div>
                  <div class="mb-3">
                     <label class="form-label">Type of Business <span class="text-danger">*</span></label>
                     <select class="form-select" id="business" name="business" required>
                        <option value="<?php echo esc_attr($update_user->type_of_business) ?>"><?php echo esc_attr($update_user->type_of_business) ?></option>
                        <option value="Stone Fabricator">Stone Fabricator</option>
                        <option value="Cabinet Installer">Cabinet Installer</option>
                        <option value="Contractor">Contractor</option>
                        <option value="Kitchen & Bath">Kitchen & Bath</option>
                        <option value="Builder">Builder</option>
                        <option value="Other (Please Specify Below)">Other (Please Specify Below)</option>
                     </select>
                     <div class="invalid-feedback">Please Select Business</div>
                  </div>
                  <div class="mb-3">
                     <label class="form-label" for="account_email">Account Billing Email <span
                        class="text-danger">*</span></label>
                     <input class="form-control" type="email" id="account_email" value="<?php echo esc_attr($update_user->account_billing_email) ?>" name="account_email" required />
                     <div class="invalid-feedback">Please enter a valid Email</div>
                     <small class="form-text text-muted">example@example.com</small>
                  </div>
                  <!-- What happened -->
                  <div class="mb-3">
                     <label class="form-label">Message </label>
                     <textarea class="form-control" name="description"  rows="3"><?php echo esc_attr($update_user->message) ?></textarea>
                  </div>
                  <div class="mb-3">
                     <div class="update-sing-image">
                    <img src="<?php echo $update_user->signature  ?>" >
                    </div>
                    <label class="form-label">Update Signature </label>
                     <canvas id="signature-pad"></canvas>
                     <input type="hidden" name="signature_data" id="signature_data" value="<?php echo $update_user->signature  ?>">
                     <span id="siClearBtn" onclick="clearSignature()">Clear</span>
                  </div>
               </div>
                                 <div class="mb-3">
                     <div class="form-check user-check">
                        <input class="form-check-input" type="checkbox" value=""  required>
                        <label class="form-check-label" for="granitegold">
                        I agree to the <a href="#">terms & conditions</a><span class="text-danger">*</span></label>
                        </label>
                        <div class="invalid-feedback">Please check terms & conditions</div>
                     </div>
                  </div>
               <div class="">
                  <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                  <a href="/change-password/" class="btn btn-primary float-end">Change Password</a>
               </div>
      </div>
      </form>
      </section>
   </div>
</div>
<?php get_footer() ?>