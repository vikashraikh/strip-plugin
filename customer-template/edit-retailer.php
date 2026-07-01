<?php
$retailer = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    if (!$retailer) {
        wp_die('Retailer not found');
    }
   $plugin_url = plugin_dir_url(__DIR__);
?>


<body class="edit-retailer">
<link rel="stylesheet" href="<?php echo $plugin_url ?>css/bootstrap.min.css"/>
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<style>
    .custom-container form {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}
</style>
<?php 

if ( isset( $_POST['update_retailer'] ) ) {
    global $wpdb;
    $uid = $_GET['id'];
    $table = 'become_authorized_retailer';
    // Collect and sanitize all fields
    if (isset($_POST['products_selected']) && is_array($_POST['products_selected'])) {
        $products_selected = implode(', ', array_map('sanitize_text_field', $_POST['products_selected']));
    } else {
        $products_selected = '';
    }
    $business=sanitize_text_field($_POST['business']);
    $otherBusiness = !empty($_POST['other_select']) ? sanitize_text_field($_POST['other_select']) : '';
    $finalBusiness = ($business === "Other" && !empty($otherBusiness)) ? $otherBusiness : $business;
    $data = [
        'business_name'              => sanitize_text_field( $_POST['business_name'] ),
        'business_owner_first'       => sanitize_text_field( $_POST['business_owner_first'] ),
        'business_owner_last'        => sanitize_text_field( $_POST['business_owner_last'] ),
        'business_owner_email'       => sanitize_email( $_POST['business_owner_email'] ),
        'phone_number'               => sanitize_text_field( $_POST['phone_number'] ),
        'address_street'             => sanitize_text_field( $_POST['address_street'] ),
        'address_city'               => sanitize_text_field( $_POST['address_city'] ),
        'address_state'              => sanitize_text_field( $_POST['address_state'] ),
        'address_postal'             => sanitize_text_field( $_POST['address_postal'] ),
        'address_country'            => sanitize_text_field( $_POST['address_country'] ),
        'onboarding_contact_first'   => sanitize_text_field( $_POST['onboarding_contact_first'] ),
        'onboarding_contact_last'    => sanitize_text_field( $_POST['onboarding_contact_last'] ),
        'onboarding_contact_phone'   => sanitize_text_field( $_POST['onboarding_contact_phone'] ),
        'onboarding_contact_email'   => sanitize_email( $_POST['onboarding_contact_email'] ),
        'type_of_business'           => $finalBusiness,
        'number_of_monthly_installs' => $_POST['number_of_monthly_installs'],
        'account_billing_email'      => sanitize_email( $_POST['account_billing_email'] ),
        'products_selected'          => $products_selected,
        'message'                    => sanitize_text_field( $_POST['message'] ),
        'status'                     => $_POST['status'],
    ];
    $where = [ 'id' => $uid ];

    $updated = $wpdb->update( $table, $data, $where );
  if ( $updated !== false && $updated > 0 ) {
  
    // If status == 1, create WP user with role retailer
    if ( intval( $_POST['status'] ) === 1 ) {
        $email = $data['onboarding_contact_email'];

        if ( ! email_exists( $email ) ) {
            $base_username = sanitize_user( $data['business_owner_first'] . $data['business_owner_last'], true );

            $username = $base_username;
            $i = 1;
            while ( username_exists( $username ) ) {
                $username = $base_username . $i;
                $i++;
            }

            $password = wp_generate_password( 12, true );

            $user_id = wp_create_user( $username, $password, $email );

        if (!is_wp_error($user_id)) {
            // Assign retailer role
            $user = new WP_User($user_id);
            $user->set_role('retailer');
            $wpdb->update($table, array('user_id' => $user_id), array('id' => $uid));
            // Login URL
           $login_url = site_url('/retailer-login/');

            // ---- SEND EMAIL TO USER ----
            $to      = $email;
            $subject = 'Your Retailer Account Is Approved';
           $body = '<!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="utf-8" />
            <title>Elite Warranty – Retailer Account Approval Confirmation</title>
            </head>
            <body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background-color:#f9f9f9;">
                <div style="max-width:600px;margin:auto;border:1px solid #2a4875;border-radius:10px;overflow:hidden;background:#ffffff;">
                    
                    <!-- Logo Section -->
                    <div style="text-align:center;background:#2a4875;padding:15px;">
                        <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/logo1-white.png" height="80px"
                             alt="Elite Warranty Logo" 
                             style="display:block;margin:0 auto;max-width:200px;height:auto;">
                    </div>
                    
                    <!-- Body Content -->
                    <div style="padding:20px;font-size:14px;color:#333333;line-height:1.6;">
                      <p>Dear <strong>'. esc_html(ucwords(trim(($data['business_owner_first'] ?? '') . ' ' . ($data['business_owner_last'] ?? '')))).'</strong>,</p>
                        <p>Congratulations! Your Retailer Account Is Now Active.</p>
                        <p>
                            <strong>Login:</strong> <a href="' . esc_url($login_url) . '" style="color:#2a4875;">' . esc_html($login_url) . '</a><br>
                            <strong>Username:</strong> ' . esc_html($_POST['onboarding_contact_email']) . '<br>
                            <strong>Password:</strong> ' . esc_html($password) . '
                        </p>
                        <p style="margin-top:25px;">Thank you,<br>
                        <strong>The Elite Surface Protection Team</strong></p><br>
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


            $headers = array(
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: Elite Surface Protection <customer-support@elitewarrantyprogram.com>'
            );

            wp_mail($to, $subject, $body, $headers);

           // ---- SEND EMAIL TO ADMIN ----
            $admin_email = get_option('admin_email');
            //$admin_email = 'pradeep.sbsgroup@gmail.com';
            $admin_subject = 'New Retailer Approved';
            
            // Use same style HTML as user email
            $admin_body = '<!DOCTYPE html>
            <html lang="en">
            <head><meta charset="utf-8" /></head>
            <body style="font-family:Arial,Helvetica,sans-serif;">
                <div style="max-width:600px;margin:auto;border:1px solid #2a4875;border-radius:10px;padding:20px;">
                    
                    <!-- Logo Section -->
                    <div style="text-align:center;background:#2a4875;padding:15px;">
                        <img src="https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/logo1-white.png" height="80px"
                             alt="Elite Warranty Logo" 
                             style="display:block;margin:0 auto;max-width:200px;height:auto;">
                    </div>
            
                    <p>Dear Admin,</p>
                    <p>
                        The retailer account for <strong>'.esc_html(ucwords($data['business_owner_first'].' '. $data['business_owner_last'])).'</strong>  
                        has been successfully activated.  
                    </p>
                    <p>
                        You can now view and manage this retailer’s details in the admin dashboard.
                    </p>
                    <p style="margin-top:25px;">Thank you,<br>
                    <strong>The Elite Surface Protection Team</strong></p><br>
                    <footer style="background:#f3f3f3;text-align:center;padding:10px;border-radius:0 0 10px 10px;">
                        <a href="https://elitewarrantyprogram.com/">www.elitewarrantyprogram.com</a>
                    </footer>
                </div>
            </body>

            </html>';
            
            // Send email
            $headers = array(
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: Elite Surface Protection <customer-support@elitewarrantyprogram.com>'
            );
            
            wp_mail($admin_email, $admin_subject, $admin_body, $headers);

        }
        }
    } 
    echo "<script>
        alert('Data Updated Successfully');
        window.location.href = '" . admin_url('admin.php?page=retailers') . "';
    </script>";
  } else {
      echo "<script>
        alert('No Changes Made Or Update Failed');
        window.location.href = '" . admin_url('admin.php?page=retailers') . "';
    </script>";
  }

    die();
}

?>
<div class="container mt-4 custom-container">

   <form method="post">
        <h3 class="text-center mb-4">Edit Retailer</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <th><label for="business_name">Business Name</label></th>
                <td><input type="text" name="business_name" id="business_name" class="regular-text"
                    value="<?php echo esc_attr( $retailer->business_name ); ?>"></td>
            </tr>
            <tr>
                <th><label for="business_owner_first">Business Owner First</label></th>
                <td><input type="text" name="business_owner_first" id="business_owner_first" class="regular-text"
                    value="<?php echo esc_attr( $retailer->business_owner_first ); ?>"></td>
            </tr>
            <tr>
                <th><label for="business_owner_last">Business Owner Last</label></th>
                <td><input type="text" name="business_owner_last" id="business_owner_last" class="regular-text"
                    value="<?php echo esc_attr( $retailer->business_owner_last ); ?>"></td>
            </tr>
            <tr>
                <th><label for="business_owner_email">Business Owner Email</label></th>
                <td><input type="email" name="business_owner_email" id="business_owner_email" class="regular-text"
                    value="<?php echo esc_attr( $retailer->business_owner_email ); ?>"></td>
            </tr>
            <tr>
                <th><label for="phone_number">Phone Number</label></th>
                <td><input type="text" name="phone_number" id="phone_number" class="regular-text"
                    value="<?php echo esc_attr( $retailer->phone_number ); ?>"></td>
            </tr>
            <tr>
                <th><label for="address_street">Street</label></th>
                <td><input type="text" name="address_street" id="address_street" class="regular-text"
                    value="<?php echo esc_attr( $retailer->address_street ); ?>"></td>
            </tr>
            <tr>
                <th><label for="address_city">City</label></th>
                <td><input type="text" name="address_city" id="address_city" class="regular-text"
                    value="<?php echo esc_attr( $retailer->address_city ); ?>"></td>
            </tr>
            <tr>
                <th><label for="address_state">State</label></th>
                <td><input type="text" name="address_state" id="address_state" class="regular-text"
                    value="<?php echo esc_attr( $retailer->address_state ); ?>"></td>
            </tr>
            <tr>
                <th><label for="address_postal">Postal Code</label></th>
                <td><input type="text" name="address_postal" id="address_postal" class="regular-text"
                    value="<?php echo esc_attr( $retailer->address_postal ); ?>"></td>
            </tr>
            <tr>
                <th><label for="address_country">Country</label></th>
                <td>
                    <input type="text" name="address_country" value="<?php echo esc_attr( $retailer->address_country ); ?>" readonly>
                </td>
            </tr>
            
             <tr>
                <th><label for="onboarding_contact_first">On-Boarding Contact</label></th>
                <td><input type="text" name="onboarding_contact_first" id="onboarding_contact_first" class="regular-text"
                    value="<?php echo esc_attr( $retailer->onboarding_contact_first ); ?>"></td>
            </tr>
            
            
             <tr>
                <th><label for="onboarding_contact_last">On-Boarding Contact Last</label></th>
                <td><input type="text" name="onboarding_contact_last" id="onboarding_contact_last" class="regular-text"
                    value="<?php echo esc_attr( $retailer->onboarding_contact_last ); ?>"></td>
            </tr>
            
            
             <tr>
                <th><label for="onboarding_contact_phone">On-Boarding Contact Phone </label></th>
                <td><input type="text" name="onboarding_contact_phone" id="onboarding_contact_phone" class="regular-text"
                    value="<?php echo esc_attr( $retailer->onboarding_contact_phone ); ?>"></td>
            </tr>
            
            <tr>
                <th><label for="onboarding_contact_email">On-Boarding Contact Email </label></th>
                <td><input type="email" name="onboarding_contact_email" id="onboarding_contact_email" readonly class="regular-text"
                    value="<?php echo esc_attr( $retailer->onboarding_contact_email ); ?>"></td>
            </tr>
            
            
            <tr>
                <th><label for="type_of_business">Type of Business</label></th>
                <?php $tbusiness = !empty($retailer->type_of_business) ? $retailer->type_of_business : ''; ?>
                <td>
                  <?php 
                    $tbusiness = !empty($retailer->type_of_business) ? $retailer->type_of_business : ''; 
                    
                    // List of fixed options
                    $fixed_options = ["Stone Fabricator", "Contractor", "Kitchen & Bath", "Builder"];
                    
                    // Decide whether to show "Other"
                    $is_other = (!empty($tbusiness) && !in_array($tbusiness, $fixed_options));
                    ?>
                    
                    <select class="form-select select-with-other" id="business" name="business" required>
                        <option value="" <?php echo $tbusiness === '' ? 'selected' : ''; ?>>-Select-</option>
                        <option value="Stone Fabricator" <?php echo $tbusiness === 'Stone Fabricator' ? 'selected' : ''; ?>>Stone Fabricator</option>
                        <option value="Contractor" <?php echo $tbusiness === 'Contractor' ? 'selected' : ''; ?>>Contractor</option>
                        <option value="Kitchen & Bath" <?php echo $tbusiness === 'Kitchen & Bath' ? 'selected' : ''; ?>>Kitchen & Bath</option>
                        <option value="Builder" <?php echo $tbusiness === 'Builder' ? 'selected' : ''; ?>>Builder</option>
                        <option value="Other" <?php echo $is_other ? 'selected' : ''; ?>>Other (Please Specify Below)</option>
                    </select>
                    
                    <div class="other-wrapper mt-2" style="<?php echo $is_other ? '' : 'display:none;'; ?>">
                        <input class="form-control" 
                               type="text" 
                               id="other_select" 
                               name="other_select" 
                               placeholder="Type Here Business Name"
                               value="<?php echo $is_other ? esc_attr($tbusiness) : ''; ?>" />
                    </div>

                </td>

            </tr>
            <tr>
                <th><label for="account_billing_email">Billing Email</label></th>
                <td><input type="email" name="account_billing_email" id="account_billing_email" class="regular-text"
                    value="<?php echo esc_attr( $retailer->account_billing_email ); ?>"></td>
            </tr>
             <?php $all_products = $wpdb->get_results("SELECT id, name FROM warranty_plans WHERE status = 1");
                $saved_products = !empty($retailer->products_selected) ? array_map('intval', explode(',', $retailer->products_selected))   : array();
            ?>
           
             <tr>
                <th><label for="message">Message</label></th>
                <td>
                <textarea name="message" id="message" class="regular-text" rows="5" style="width:100%;"><?php 
                    echo esc_textarea( $retailer->message ); 
                ?></textarea>
            </td>
            </tr>
           <tr>
            <th><label for="signature">Signature</label></th>
                <td>
                    <?php if ( !empty($retailer->signature) ) : ?>
                        <img src="<?php echo esc_url($retailer->signature); ?>" 
                             alt="Signature" 
                             style="max-width:200px; height:auto; border:1px solid #ccc; padding:3px;">
                    <?php else : ?>
                        <em>No signature available</em>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="status">Status</label></th>
                <td>
                   <select name="status" id="status" <?php if ($retailer->status == 1) echo 'disabled'; ?>>
                    <option value="0" <?php selected($retailer->status, 0); ?>>Pending</option>
                    <option value="1" <?php selected($retailer->status, 1); ?>>Approved</option>
                    <option value="2" <?php selected($retailer->status, 2); ?>>Declined</option>
                </select>
                
                <?php if ($retailer->status == 1): ?>
                    <input type="hidden" name="status" value="1">
                <?php endif; ?>
                </td>
            </tr>
        </table>

        <p class="submit update-submit">
            <input type="submit" name="update_retailer" id="update_retailer" class="btn btn-primary mt-3 button button-primary" value="Update Retailer">
             <a href="<?php echo admin_url('admin.php?page=retailers'); ?>" class="btn auto_btn plan-btn back-btn" style="margin-top:0px;margin-left:15px;position: relative;top: -3px;">Back</a>
        </p>
    </form>
</div>
<script src="<?php echo plugin_dir_url(__DIR__); ?>js/jquery-min.js"></script>
<script src="<?php echo plugin_dir_url(__DIR__); ?>js/custom.js"></script>
</body>
</html>