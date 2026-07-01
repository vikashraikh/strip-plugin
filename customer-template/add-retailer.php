<body class="edit-retailer">
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<link rel="stylesheet" href="<?php echo plugin_dir_url(__DIR__); ?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo plugin_dir_url(__DIR__); ?>css/style.css">
<?php

if (isset($_POST['add_retailer'])) {
    global $wpdb;
    $error = "";
    $success = "";

    $plugin_url = plugin_dir_url(__DIR__);
    $table = 'become_authorized_retailer';

    $onboarding_email = sanitize_email($_POST['on-boarding_email']);
    $first_name = sanitize_text_field($_POST['customer_first_name']);
    $business=sanitize_text_field($_POST['business']);
    $otherBusiness = !empty($_POST['other_select']) ? sanitize_text_field($_POST['other_select']) : '';
    $finalBusiness = ($business === "Other" && !empty($otherBusiness)) ? $otherBusiness : $business;
    // Check if email already exists
    $email_exists = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE onboarding_contact_email = %s", $onboarding_email)
    );

    if ($email_exists > 0) {
        $error = "❌ This onboarding contact email is already registered.";
    } else {
        $products = isset($_POST['authorized_products']) ? implode(', ', array_map('sanitize_text_field', $_POST['authorized_products'])) : '';

        // Handle signature
        $signature_url = '';
        if (!empty($_POST['signature_data'])) {
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'] . '/signatures/';
            $upload_url  = $upload_dir['baseurl'] . '/signatures/';

            if (!file_exists($upload_path)) {
                wp_mkdir_p($upload_path);
            }

            $data = str_replace('data:image/png;base64,', '', $_POST['signature_data']);
            $data = str_replace(' ', '+', $data);
            $imageData = base64_decode($data);
            $fileName = 'signature_' . time() . '.png';
            file_put_contents($upload_path . $fileName, $imageData);
            $signature_url = $upload_url . $fileName;
        }

        // Insert into DB
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
            'type_of_business' => $finalBusiness,
            'number_of_monthly_installs' => sanitize_text_field($_POST['plan_number']),
            'account_billing_email' => sanitize_email($_POST['account_email']),
            'products_selected' => $products,
            'signature' => $signature_url,
            'message' => sanitize_textarea_field($_POST['description']),
            'submitted_at' => current_time('mysql'),
        ]);

        if ($inserted) {
    $success = "✅ Form submitted successfully!";
    $website_name  = get_bloginfo('name');
    $support_email = get_option('admin_email');
    $subject = 'Welcome! Thank You For Becoming An Elite Surface Protection Retailer';

    // Build HTML email body
    $body = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Welcome To The Elite Surface Protection Retailer Program!</title>
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
                <p>Dear <strong>'. esc_html(ucwords(trim(($data['customer_first_name'] ?? '') . ' ' . ($data['customer_last_name'] ?? '')))).'</strong>,</p>
                
                <p>Thank you for requesting with <strong>Elite Surface Protection</strong>. 
                We have successfully received your details, and your account is now <strong>pending approval</strong>.</p>

                <p>Once your account is approved, you will receive an email with your login instructions 
                and access to all member features.</p>

                <p>If you have any questions in the meantime, feel free to contact us at 
                <a href="mailto:' . esc_html($support_email) . '" style="color:#2a4875;text-decoration:none;">' . esc_html($support_email) . '</a>.</p>

                <p>We appreciate your interest in joining our community and look forward to having you on board!</p>

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
    $mail_sent = wp_mail($onboarding_email, $subject, $body, $headers);
    
    if (!$mail_sent) {
        error_log("Email to {$onboarding_email} failed to send.");
    }
    } else {
        $error = "❌ There was an error submitting the form. Please try again.";
    }
    $admin_email = get_option('admin_email');
    $subject_admin = 'New Retailer Registration Submitted – Elite Surface Protection Retailer Program';

    // Build HTML email body for admin
    $body_admin = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>New Retailer Registration</title>
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
                    <p>A new retailer request form has been submitted on <strong>' . esc_html(get_bloginfo('name')) . '</strong>.</p>
        
                    <p><strong>Submitted Details:</strong></p>
                    <ul>
                        <li><strong>Business Owner:</strong> ' . esc_html(ucwords($_POST['customer_first_name'] . ' ' . $_POST['business_owner_last'])) . '</li>
                        <li><strong>Email:</strong> ' . esc_html($onboarding_email) . '</li>
                        <li><strong>Phone:</strong> ' . esc_html($_POST['customer_phone']) . '</li>
                        <li><strong>Business Name:</strong> ' . esc_html($_POST['business_name']) . '</li>
                    </ul>
        
                    <p>You can review and approve this retailer in the admin dashboard.</p>
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
}
?>

<style>
.elite-custom-form{margin:8.7em 0}
.elite-custom-form .Input-box{width:100%;max-width:100%;padding:12px;height:auto;background:transparent!important;border:1px solid #2a4875;color:#000}
.elite-custom-form .btn{padding:18px 35px;font-family:"Roboto",Sans-serif;font-size:14px;font-weight:500;line-height:11px;text-shadow:0 0 0 #0000004d;color:#FFF;
border-style:none;border-radius:0 0 0 0;box-shadow:0 0 0 0 #00000080;background:#2a4875}
.elite-custom-form input,.elite-custom-form input::placeholder{background-color:Transparent!important;color:#000}
.elite-custom-form a{color:#2a4875;text-decoration:none}
.elite-custom-top h3{font-size:28px;font-weight:700;line-height:44px;color:#000;text-align:center;text-transform:capitalize}
.elite-custom-top p{font-size:16px;font-weight:400;line-height:24px;color:#000;text-align:center;text-transform:capitalize}
.user-check .form-check-label{margin-left:15px}
.edit-retailer label{margin-left:0px;}
.edit-retailer label.form-check-label{margin-left:12px;}
.form-check-input, .form-check-radio {
    margin-right: 2px;
    width: 22px !important;
    height: 22px !important;
}
.select2-container .select2-selection--single{    height: 49px;display: flex; align-items: center;}
.select2-container--default .select2-selection--single .select2-selection__arrow {top: 10px;}
.form_bg input[type=checkbox]:checked::before{display:none;}
.form_bg .form-check-input {
    margin-top: 6px !important;
}
.add-retailer .form-selected {width:100%;}
</style>
<div class="container mt-4 custom-container add-retailer">
<?php if (!empty($error)) : ?><div class="alert alert-danger" role="alert"> <?php echo  $error ?></div><?php endif; ?>
<?php if (!empty($success)) : ?><div class="alert alert-success" role="alert"> <?php echo  $success ?></div><?php endif; ?>
   

    <form method="post" id="warranty-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"

    enctype="multipart/form-data" onsubmit="saveSignature()" class="form-validation form_bg" novalidate >
     <h3 class="text-center mb-4">Become An Authorized Retailer</h3>

    <div class="row">

        <!-- Business Info -->
        <div class="col-12 mb-3">
            <label for="business-name" class="form-label">Business Name <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="business-name" name="buisiness-name" required>
            <div class="invalid-feedback">Please enter your business name</div>
        </div>

        <!-- Owner Name -->
        <div class="col-12 mb-12" bis_skin_checked="1">
        <label for="customer_first_name" class="form-label">Business Owner<span class="text-danger">*</span></label>
      </div>
        <div class="col-6 mb-3">
            <input class="form-control" type="text" id="customer_first_name" name="customer_first_name" required>
            <div class="invalid-feedback">Please enter your first name</div>
            <small class="form-text text-muted">First</small>
        </div>
        <div class="col-6 mb-3">
            <input class="form-control" type="text" id="customer_last_name" name="customer_last_name" required>
            <div class="invalid-feedback">Please enter your last name</div>
            <small class="form-text text-muted">Last</small>
        </div>

        <!-- Owner Email -->
        <div class="col-12 mb-3">
            <label for="customer_email" class="form-label">Business Owner Email <span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="customer_email" name="customer_email" required>
            <div class="invalid-feedback">Please enter a valid email address</div>
            <small class="form-text text-muted">example@example.com</small>
        </div>

        <!-- Phone -->
        <div class="col-12 mb-3">
            <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
            <input class="form-control" type="tel" name="customer_phone" id="customer_phone" maxlength="10" required>
            <div class="invalid-feedback">Please enter a valid phone number</div>
        </div>

        <!-- Address -->
        <div class="col-12 mb-3">
            <label for="address_line1" class="form-label">Business Address <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="address_line1" name="address_line1" required>
            <div class="invalid-feedback">Please enter an address</div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="city" name="city" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="state" class="form-label">State/Region/Province <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="state" name="state" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="zip" class="form-label">Postal / Zip Code <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="zip" name="zip" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="country-select" class="form-label">Country <span class="text-danger">*</span></label>
            <select name="country-select" id="country-select" class="form-control" required>
                <option value="">-- Select Country --</option>
                <?php
                global $wpdb;
                $countries = $wpdb->get_results("SELECT country_id, country_name FROM geo_countries");
                foreach ($countries as $country) {
                    echo '<option value="' . esc_attr($country->country_id) . '">' . esc_html($country->country_name) . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- Onboarding Contact -->
        <div class="col-6 mb-3">
            <label for="on-boarding_first_name" class="form-label">Onboarding First Name <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="on-boarding_first_name" name="on-boarding_first_name" required>
        </div>
        <div class="col-6 mb-3">
            <label for="on-boarding_last_name" class="form-label">Onboarding Last Name <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="on-boarding_last_name" name="on-boarding_last_name" required>
        </div>
        <div class="col-6 mb-3">
            <label for="on-boarding_phone" class="form-label">Onboarding Phone <span class="text-danger">*</span></label>
            <input class="form-control" type="tel" id="on-boarding_phone" name="on-boarding_phone" maxlength="10" required>
        </div>
        <div class="col-6 mb-3">
            <label for="on-boarding_email" class="form-label">Onboarding Email <span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="on-boarding_email" name="on-boarding_email" required>
        </div>

        <!-- Business Type -->
        <div class="col-12 mb-3">
            <label class="form-label">Type of Business <span class="text-danger">*</span></label>
            <select class="form-selected select-with-other" id="business" name="business" required>
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

        <!-- Billing Email -->
        <div class="col-12 mb-3">
            <label for="account_email" class="form-label">Account Billing Email <span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="account_email" name="account_email" required>
        </div>

        <!-- Message -->
        <div class="col-12 mb-3">
            <label class="form-label">Message</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
        </div>

        <!-- Terms -->
        <!--<div class="col-12 mb-3">-->
        <!--    <div class="form-check">-->
        <!--        <input class="form-check-input" type="checkbox" id="agree_terms" required>-->
        <!--        <label class="form-check-label" for="agree_terms">I agree to the <a href="#">terms & conditions</a></label>-->
        <!--    </div>-->
        <!--</div>-->

        <!-- Signature -->
        <div class="col-12 mb-3">
             <canvas id="signature-pad"></canvas>

             <input type="hidden" name="signature_data" id="signature_data">

             <span id="siClearBtn" onclick="clearSignature()">Clear</span>
        </div>

        <!-- Submit -->
        <div class="text-center">
            <button type="submit" name="add_retailer" class="btn btn-primary mt-3">Submit</button>
           <a href="<?php echo admin_url('admin.php?page=retailers'); ?>" class="btn btn-back" style="margin-top:16px;margin-left:15px">Back</a>
        </div>

    </div>
</form>
<script src="<?php echo plugin_dir_url(__DIR__); ?>js/jquery-min.js"></script>
<script src="<?php echo plugin_dir_url(__DIR__); ?>js/signature_pad.umd.min.js"></script>
<script src="<?php echo plugin_dir_url(__DIR__); ?>js/custom.js"></script>
</div>