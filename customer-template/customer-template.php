<?php
/**
 * Template Name: Customer Template
 */
function uim_enqueue_admin_assets()
{
  $plugin_url = plugin_dir_url(__DIR__);
  wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
  wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
  wp_enqueue_style('style-css1', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
  wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
  wp_enqueue_script('canender', $plugin_url . '/js/jquery-ui.min.js' ,array('jquery'), null, true);
  wp_enqueue_script('warrant-form', $plugin_url . 'js/warranty-form.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');

get_header();
$plugin_url = plugin_dir_url(__DIR__);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;
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
    $table = 'warranty_claims';
    $phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
     $countertop=sanitize_text_field($_POST['countertop_type']);
    $othercountertop = !empty($_POST['other_countertop']) ? sanitize_text_field($_POST['other_countertop']) : '';
    $finalcountertop = ($countertop === "Other" && !empty($othercountertop)) ? $othercountertop : $countertop;
    $room=sanitize_text_field($_POST['room']);
    $otherroom = !empty($_POST['other_room']) ? sanitize_text_field($_POST['other_room']) : '';
    $finalroom = ($room === "Other" && !empty($otherroom )) ? $otherroom  : $room;
     $problem=sanitize_text_field($_POST['problem']);
    $otherproblem = !empty($_POST['other_problem']) ? sanitize_text_field($_POST['other_problem']) : '';
    $finalproblem = ($problem === "Other" && !empty( $otherproblem )) ?  $otherproblem  : $problem;
    
    $uploaded_image_urls = [];
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if (!empty($_FILES['damage_photos']['name'][0])) {
        $file_count = count($_FILES['damage_photos']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['damage_photos']['error'][$i] === 0) {
                $file = [
                    'name'     => $_FILES['damage_photos']['name'][$i],
                    'type'     => $_FILES['damage_photos']['type'][$i],
                    'tmp_name' => $_FILES['damage_photos']['tmp_name'][$i],
                    'error'    => $_FILES['damage_photos']['error'][$i],
                    'size'     => $_FILES['damage_photos']['size'][$i],
                ];
                $_FILES['single_image'] = $file;
                $attachment_id = media_handle_upload('single_image', 0);

                if (!is_wp_error($attachment_id)) {
                    $image_url = wp_get_attachment_url($attachment_id);
                    $uploaded_image_urls[] = esc_url_raw($image_url);
                }
            }
        }
    }

    $images_serialized = maybe_serialize($uploaded_image_urls);

    $inserted = $wpdb->insert($table, [
        'plan_type'              => sanitize_text_field($_POST['plan_type']),
        'first_name'             => sanitize_text_field($_POST['customer_first_name']),
        'last_name'              => sanitize_text_field($_POST['customer_last_name']),
        'address_line1'          => sanitize_text_field($_POST['address_line1']),
        'address_line2'          => sanitize_text_field($_POST['address_line2']),
        'city'                   => sanitize_text_field($_POST['city']),
        'state'                  => sanitize_text_field($_POST['state']),
        'zip'                    => sanitize_text_field($_POST['zip']),
        'phone'                  => $phone,
        'email'                  => sanitize_email($_POST['customer_email']),
        'plan_number'            => sanitize_text_field($_POST['plan_number']),
        'fabricator'             => sanitize_text_field($_POST['customer_fabricator']),
        'countertop_type'        => $finalcountertop,
        'room'                   => $finalroom,
        'problem'                => $finalproblem,
        'chip_at_sink'           => sanitize_text_field($_POST['chip_at_sink']),
        'description'            => sanitize_textarea_field($_POST['description']),
        'damage_during_delivery' => sanitize_text_field($_POST['damage_during_delivery']),
        'install_date'           => sanitize_text_field($_POST['install_date']),
        'damage_date'            => sanitize_text_field($_POST['damage_date']),
        'attempt_clean'          => sanitize_text_field($_POST['attempt_clean']),
        'damage_photos'          => $images_serialized,
        'submitted_at'           => current_time('mysql'),
    ]);
    if ($inserted) {
    $success = "✅ Protection Plan Claim Form submitted successfully!";
    $website_name  = get_bloginfo('name');
    $support_email = get_option('admin_email');
    $subject = 'Your Protection Plan Claim Form Has Been Received.';
    $user_email = $_POST['customer_email'];
    // Build HTML email body
    $body = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Your Protection Plan Claim Form Has Been Received!</title>
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
                <p>Dear <strong>' . esc_html(ucwords($_POST['customer_first_name'] . ' ' . $_POST['customer_last_name'])) . '</strong>,</p>

                <p><strong>Warranty Number :</strong>'.($_POST['plan_number']).'</p>
                
                <p>Thank you for contacting <strong>Elite Surface Protection.</strong> We’ve received your request, and our support team is reviewing the details.</p>

                <p>If you have any questions in the meantime, feel free to contact us at 
                <a href="mailto:' . esc_html($support_email) . '" style="color:#2a4875;text-decoration:none;">' . esc_html($support_email) . '</a>.</p>

                <p>Thank you for choosing <strong>Elite Surface Protection</strong> and trusting us with your protection needs.</p>

                <p style="margin-top:25px;">Thank you,<br>
                <strong>The Elite Surface Protection Team</strong></p>
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
    $mail_sent = wp_mail($user_email, $subject, $body, $headers);
    
    if (!$mail_sent) {
        error_log("Email to {$user_email} failed to send.");
    }
    } else {
        $error = "❌ There was an error submitting the form. Please try again.";
    }
    $admin_email = get_option('admin_email');
    //$admin_email = 'pradeep.sbsgroup@gmail.com';
    $subject_admin = 'New Protection Plan Claim Form Has Been Received – Elite Warranty Program';

    // Build HTML email body for admin
    $body_admin = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>New Protection Plan Claim Form Received</title>
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

                    <p>A new protection plan claim form has been submitted on <strong>' . esc_html(get_bloginfo('name')) . '</strong>.</p>
        
                    <p><strong>Submitted Details:</strong></p>
                    <ul>
                        <li><strong>Warranty Number :</strong>'.$_POST['plan_number'].'</li>
                        <li><strong>Business Owner:</strong> ' . esc_html(ucwords($_POST['customer_first_name'] . ' ' . $_POST['customer_last_name'])) . '</li>
                        <li><strong>Email:</strong> ' . esc_html($user_email) . '</li>
                        <li><strong>Phone:</strong> ' . esc_html($_POST['customer_phone']) . '</li>
                    </ul>
        
                    <p>You can review in the admin dashboard.</p>
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
  } else {
      // ❌ reCAPTCHA failed
        $error = "❌ ReCAPTCHA Verification Failed. Please Try Again."; }
}

?>

<style>
   .entry-hero { visibility: visible;height: auto; margin-top:0;}
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
              <a href="<?php echo site_url(); ?>" title="Home" itemprop="url"
                class="code4rest-bc-home code4rest-bc-home-icon"><span><span class="code4rest-svg-iconset svg-baseline">
                    <svg aria-hidden="true" class="code4rest-svg-icon code4rest-home-svg" fill="currentColor"
                      version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                      <title>Home</title>
                      <path d="M9.984 20.016h-4.969v-8.016h-3l9.984-9 9.984 9h-3v8.016h-4.969v-6h-4.031v6z"></path>
                      </svg>
                          </a>
                      </span>
                      <span class="bc-delimiter">/</span>
                       <span><a href="/for-consumers/" itemprop="url"><span>For Consumers</span></a></span>
                       <span class="bc-delimiter">/</span> 
                       <span class="code4rest-bread-current"><?php echo the_title(); ?></span>
          </div>
        </nav>
      </header>
    </div>
  </div>
</section>
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
    <?php endwhile; ?>
<?php endif; ?>
<style>
.success-message {color: green;
    font-size: 20px;
    font-weight: 500;
    background: #fff;
    padding: 15px;
    max-width: 600px;
    margin: 0 auto;}
/* General form container */
.file-a-claim .mt-3{margin:5px 0px!important;}
.file-a-claim .mb-3{margin:5px 0px!important;}
.file-a-claim form .image-remove-btn{padding: 5px 6px!important;background: red !important;}
.file-a-claim form {
  max-width: 100%; margin: 40px auto;
  padding: 40px;background: #fff;
  border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  font-family: 'Segoe UI', sans-serif;
}

/* Form labels */
.file-a-claim form label {
  display: block;font-weight: 600;
  margin-bottom: 6px; color: #333;
}

/* Required asterisk */
.file-a-claim form label .wpcf7-required {
  color: red;
}

/* Inputs, textareas, selects */
.file-a-claim form input[type="text"],
.file-a-claim form input[type="email"],
.file-a-claim form input[type="tel"],
.file-a-claim form select,
.file-a-claim form textarea {
  width: 100%; padding: 12px 14px;
  font-size: 15px; border: 1px solid #ccc;
  border-radius: 8px;box-sizing: border-box;
  transition: border-color 0.3s ease;margin-bottom: 10px;
  background-color: #f9f9f9;
}

.file-a-claim form input:focus,
.file-a-claim form textarea:focus,
.file-a-claim form select:focus {
  border-color: #2a4875;
  outline: none; background-color: #fff;
}

/* Name & Address rows */
.file-a-claim form .form-row,
.file-a-claim form .name-row,
.file-a-claim form .address-row {
  display: flex; flex-wrap: wrap;
  gap: 20px;
}

.file-a-claim form .form-row > div,
.file-a-claim form .name-row > div,
.file-a-claim form .address-row > div {
  flex: 1; min-width: 200px;
}

/* Submit button */
.file-a-claim form input[type="submit"],
.file-a-claim form button[type="submit"] {
  background-color: #2a4875; color: white;
  padding: 14px 24px;border: none;
  border-radius: 8px; font-size: 16px; cursor: pointer;
  transition: background-color 0.3s ease;
}

.file-a-claim form input[type="submit"]:hover,
.file-a-claim form button[type="submit"]:hover {
  background-color: #000000;
}
.file-a-claim .form-text {
    color: #333 !important;
}
/* Dropdowns (select) and textareas */
.file-a-claim form select,
.file-a-claim form textarea {
  width: 100%;padding: 12px 14px;
  font-size: 15px; border: 1px solid #ccc;
  border-radius: 8px; background-color: #f9f9f9;
  box-sizing: border-box;transition: border-color 0.3s ease;
  margin-bottom: 10px;
}

.file-a-claim form select:focus,
.file-a-claim form textarea:focus {
  border-color: #2a4875;
  background-color: #fff;
  outline: none;
}

/* Textarea specific */
.file-a-claim form textarea {
  resize: vertical;
  min-height: 100px;
}


.file-a-claim .form-check-input[type="radio"] {
  width: 16px !important;
  height: 16px !important;padding-top:8px!important;
  margin-top: 10px!important;
  margin-right: 6px !important;
  accent-color: #2a4875 !important;
  appearance: auto !important;
  cursor: pointer;
}

.file-a-claim form .radio-question p {
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

/* STACK radio buttons vertically */
.file-a-claim .form-check {
min-width:100px;
}

/* Optional: on focus (keyboard nav) */
.file-a-claim .form-check-input:focus {
  outline: 2px solid #80bdff;
  outline-offset: 2px;
}

/* Highlight label when radio is checked */
.file-a-claim .form-check-input[type="radio"]:checked + .form-check-label {
  color: #2a4875;
  font-weight: 600;
}

/* Form label styling (question text) */
.file-a-claim .form-label {
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
  color: #222;
}


/* Responsive adjustments */
@media (max-width: 600px) {
 .file-a-claim form .form-row,
 .file-a-claim form .name-row,
 .file-a-claim form .address-row {
    flex-direction: column;
  }
}

</style>
<div class="container mt-5 mb-5 file-a-claim container-width">
  <h3 class="text-center mb-5">Protection Plan Claim Form</h3>
   <?php
    if (isset($inserted)) {
        if ($inserted) {
            echo '<div id="form-message" class="sucsess-true mt-3">';
            echo '<p class="text-center success-message">Thank You For Submitting Claim Details.</p>';
            echo '</div>';
        } else {
            echo '<div id="form-message" class="alert alert-danger text-center">';
            echo 'Failed To Submit The Claim. Please Try Again.';
            echo '</div>';
        }
    } elseif (!empty($error)) {
        echo '<div id="form-message" class="alert alert-danger text-center">';
        echo esc_html($error);
        echo '</div>';
    }
    ?>
  <form method="post" id="warranty-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"
    enctype="multipart/form-data" class="needs-validation warranty_id" novalidate>
    <div class="row">
      <!-- Protection Plan Number -->
      <div class="mb-3" id="plan__wrapper">
        <label class="form-label" for="plan_number">What is your Protection Plan Number? <span
            class="text-danger">*</span></label>
        <input class="form-control" type="text" id="plan_number" name="plan_number" placeholder="Plan Number" required />
        <small class="form-text text-muted">If you do not know your Protection Plan Number, please use your phone
          number.</small>
        <div class="invalid-feedback" id="plan-error">Please Enter a Valid Protection Plan Number</div>
      </div>

      <div class="col-12 mb-12"> <label for="customer_first_name" class="form-label">Name.<span
            class="text-danger">*</span></label></div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="customer_first_name" name="customer_first_name" placeholder="First Name" required />
        <div class="invalid-feedback">Please Enter Your First Name</div>
      </div>
      <div class="col-6 mb-6">
        <input class="form-control" type="text" id="customer_last_name" name="customer_last_name" placeholder="Last Name" required />
        <div class="invalid-feedback">Please Enter Your Last Name</div>
      </div>
      <div class="col-12 mt-3">
        <label class="form-label" for="address_line1">Address <span class="text-danger">*</span></label>
        <input class="form-control" type="text" id="address_line1" name="address_line1" required />
        <div class="invalid-feedback">Please Enter Valid Address </div>
         <small class="form-text text-muted"> Address Line 2</small>
        <input class="form-control mt-3" type="text" id="address_line2" name="address_line2" />
      </div>
      <div class="row m-0 p-0">
        <div class="col-md-4 mb-2">
          <small class="form-text text-muted"> City</small> <span class="text-danger">*</span>
          <input class="form-control mt-3" type="text" id="city" name="city" placeholder="City" required />
          <div class="invalid-feedback">Please Enter City</div>
        </div>
        <div class="col-md-4 mb-2">
         <small class="form-text text-muted"> State/Region/Province</small> <span class="text-danger">*</span>
          <input class="form-control mt-3" type="text" id="state" name="state" placeholder="State/Region/Province" required />
          
          <div class="invalid-feedback">Please Enter State</div>
        </div>
        <div class="col-md-4 mb-2">
         <small class="form-text text-muted"> Postal / Zip Code</small> <span class="text-danger">*</span>
          <input class="form-control mt-3" type="text" id="zip" name="zip" placeholder="Postal / Zip Code" required />
          <div class="invalid-feedback">Please Enter Zip Code</div>
        </div>
      </div>
      <!-- Phone -->
      <div class="mb-3">
        <label for="customer_phone" class="form-label mt-2">Best Contact Phone <span
            class="text-danger">*</span></label>
        <input class="form-control" type="tel" name="customer_phone" id="customer_phone" placeholder="Contact Phone" required maxlength="10" />
        <div class="invalid-feedback">Please Enter Valid Contact Number</div>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="form-label" for="customer_email">Email <span class="text-danger">*</span></label>
        <input class="form-control" type="email" id="customer_email" name="customer_email" placeholder="Email" required />
        <div class="invalid-feedback">Please enter a valid Email</div>
      </div>

      <!-- Fabricator -->
      <div class="mb-3">
        <label for="customer_fabricator" class="form-label">Who was your countertop fabricator/installer? <span
            class="text-danger">*</span></label>
        <input class="form-control" type="text" id="customer_fabricator" name="customer_fabricator" required />
        <div class="invalid-feedback">Please enter a countertop fabricator</div>
      </div>

      <!-- Type of Countertop -->
      <div class="mb-3">
        <label for="countertop_type" class="form-label">What type of countertop do you have? <span
            class="text-danger">*</span></label>
        <select class="form-select select-with-other" id="countertop_type" name="countertop_type" required>
          <option value="">-Select-</option>
          <option value="Granite">Granite</option>
          <option value="Quartz">Quartz</option>
          <option value="Quartzite">Quartzite</option>
          <option value="Solid Surface">Solid Surface</option>
          <option value="Don't Know">Don't Know</option>
          <option value="Other"> Other</option>
        </select>
        <div class="other-wrapper mt-2" style="display:none;">
            <input class="form-control" type="text" id="other_countertop" placeholder="Please Enter Here" name="other_countertop" />
        </div>
        <div class="invalid-feedback">Please Select countertop type</div>
      </div>

      <!-- Room -->
      <div class="mb-3">
        <label class="form-label">In which room is the damaged countertop? <span class="text-danger">*</span></label>
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

      <!-- Problem -->
      <div class="mb-3">
        <label class="form-label">Tell us what the problem is with your countertop <span
            class="text-danger">*</span></label>
        <select class="form-select select-with-other" id="problem" name="problem" required>
          <option value="">-Select-</option>
          <option value="Stain - Food & Beverage">Stain - Food & Beverage</option>
          <option value="Stain - Oil based or Non Household">Stain - Oil based or Non Household</option>
          <option value="Damage - Chip">Damage - Chip</option>
          <option value="Damage - Scratch(es)">Damage - Scratch(es)</option>
          <option value="Damage - Crack">Damage - Crack</option>
          <option value="Damage - Pitting">Damage - Pitting</option>
          <option value="Damage - Dulling of Surface/Etching">Damage - Dulling of Surface/Etching</option>
          <option value="Damage - Caulking">Damage - Caulking</option>
          <option value="Damage - Hard Water Mark or Deposit">Damage - Hard Water Mark or Deposit</option>
          <option value="Other"> Other</option>
        </select>
         <div class="other-wrapper mt-2" style="display:none;">
            <input class="form-control" type="text" id="other_problem" placeholder="Please Enter Here" name="other_problem" />
        </div>
        <div class="invalid-feedback">Please Select problem</div>
      </div>

    <!-- Chip at sink? -->
        <div class="mb-3 file-a-claim">
          <label class="form-label">If you chipped your countertop, did it occur at the sink?</label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="chip_at_sink" id="chipYes" value="yes">
              <label class="form-check-label" for="chipYes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="chip_at_sink" id="chipNo" value="no">
              <label class="form-check-label" for="chipNo">No</label>
            </div>
          </div>
        </div>
      <!-- What happened -->
      <div class="mb-3">
        <label class="form-label">Tell us what happened and how it happened. <span class="text-danger">*</span></label>
        <textarea class="form-control" name="description" rows="3" required></textarea>
        <div class="invalid-feedback">This field is required</div>
      </div>

  <!-- Damage during installation/delivery -->
        <div class="mb-3 file-a-claim">
          <label class="form-label">
            Did the damage occur during installation or delivery? <span class="text-danger">*</span>
          </label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="damage_during_delivery" id="deliveryYes" value="yes" required>
              <label class="form-check-label" for="deliveryYes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="damage_during_delivery" id="deliveryNo" value="no">
              <label class="form-check-label" for="deliveryNo">No</label>
            </div>
          </div>
        </div>


      <!-- Installation Date -->
      <div class="mb-3">
        <label class="form-label">Countertop Installation Date?</label>
        <input type="text" id="install_date" name="install_date" class="form-control" placeholder="MM-DD-YY" required>
        <div class="invalid-feedback">Please enter a Countertop Installation Date</div>
      </div>

      <!-- Damage Date -->
      <div class="mb-3">
        <label class="form-label">Date the Stain or Damage Occurred?</label>
        <input type="text" id="damage_date" class="form-control" name="damage_date" placeholder="MM-DD-YY" required />
        <small class="form-text text-muted">For your claim to be accepted, the damage must be reported within 30 days of
          occurring.</small>
        <div class="invalid-feedback">Please enter a Damage Occurred</div>
      </div>

      <!-- Attempt to clean or repair -->
        <div class="mb-3 file-a-claim">
          <label class="form-label">
            Did you attempt to clean or repair it? <span class="text-danger">*</span>
          </label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="attempt_clean" id="cleanYes" value="yes" required>
              <label class="form-check-label" for="cleanYes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="attempt_clean" id="cleanNo" value="no">
              <label class="form-check-label" for="cleanNo">No</label>
            </div>
          </div>
        </div>

      <!-- Photos Required Notice -->
      <div class="my-2 ">
        <h5 class="text-danger fw-bold text-center">PHOTOS REQUIRED</h5>
        <p class="text-center">
          <strong>In order for us to process your claim, we require the following images:</strong>
        </p>
        <ul class="text-center list-unstyled">
          <li>- Images that capture the entire surface area of your countertop(s), ideally taken from <strong>6–10 feet
              away</strong></li>
          <li>- Images that capture each damaged area, ideally taken from <strong>2 feet away</strong></li>
        </ul>
        <p class="text-center mb-1">You can upload a maximum of 5 images</p>
        <h6 class="fw-bold text-center mt-3">Here are some Examples:</h6>
        <div class="ex-images">
          <div>
            <img src="<?php echo $plugin_url . 'images/GG1.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_2.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_3.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_5.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/GG2.jpg' ?>">
          </div>
          <div>
            <img src="<?php echo $plugin_url . 'images/Example_Photo_4.jpg' ?>">
          </div>
        </div>
      </div>
      <!-- Upload Section -->
      <div class="mt-2  text-center">
        <h5 class="fw-bold text-uppercase">Upload Images Here</h5>
        <p class="fw-semibold text-uppercase">Click submit button below when completed</p>

        <!-- File Upload -->
        <div id="imageUpload_wrapper" class="mb-3 text-start">
          <label for="imageUpload" class="form-label fw-bold">Image Upload <span class="text-danger">*</span></label>
          <div class="wrapper">
            <div class="drop" id="drop-area">
              <div class="cont">
                <img src="<?php echo $plugin_url . 'images/10254947.png' ?>" width="57">
                <div class="desc">
                  your files to Assets, or
                </div>
                <div class="browse" id="browse-trigger">
                  click here to browse
                </div>
              </div>
              <output id="list"></output>
              <div id="file-inputs">
              <input class="form-control" type="file" id="imageUpload" name="damage_photos[]" multiple
                accept=".jpg,.jpeg,.png" required>
                </div>
            </div>
          </div>
          <small class="form-text text-muted">Please take a look at the examples of images below before uploading your
            images.</small>
          <ul id="fileNamesList"></ul>
          <div class="invalid-feedback"> Please Upload Image </div>
        </div>
        <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_KEY" style="margin-top: 30px;"></div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>

        <!-- Thank You Note -->
        <div class="mt-4">
          <p>Please allow us 24–48 hours for us to respond.</p>
        </div>
        
      </div>
    </div>
  </form>
   <!-- Load reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
 </div>
<?php get_footer(); ?>
<script>
$(function(){
    $("#install_date").datepicker({
    dateFormat: "mm-dd-yy",
    maxDate: 0,
    changeMonth: true,
    changeYear: true
    });
    $("#damage_date").datepicker({
    dateFormat: "mm-dd-yy",
    maxDate: 0,
    changeMonth: true,
    changeYear: true
});
    $('#warranty-form').on('submit', function (e) {
        let list = $('#list .preview-item').length;
        let $feedback =$('#imageUpload_wrapper').find('.invalid-feedback');
        if(list === 0){
             e.preventDefault();
            $feedback.show();
            $('html, body').animate({
            scrollTop: $('#imageUpload_wrapper').offset().top - 100
        }, 500);
        }
        else{
            $feedback.hide();
        }
    })
    
    $("#plan_number").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, ""); 
  });
});
document.addEventListener("DOMContentLoaded", function() {
    var msg = document.getElementById("form-message");
    if (msg) {
        msg.scrollIntoView({ behavior: "smooth", block: "center" });
    }
});

</script>