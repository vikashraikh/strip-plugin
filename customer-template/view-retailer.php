<?php
$retailer = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    if (!$retailer) {
        wp_die('Retailer not found');
    }
   $plugin_url = plugin_dir_url(__DIR__);
?>


<body>
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $plugin_url ?>css/bootstrap.min.css"/>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<div class="container mt-4 custom-container">
    <div class="claim-card">
        <h3 class="text-center mb-4">Retailer Details</h3>

        <div class="detail-row">
            <div class="detail-label">Business Name</div>
            <div class="detail-value"><?php echo esc_html( $retailer->business_name ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Business Owner</div>
            <div class="detail-value"><?php echo esc_html(ucfirst($retailer->business_owner_first) . ' ' . ucfirst($retailer->business_owner_last)); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Business Owner Email</div>
            <div class="detail-value detail-email">
                <?php echo esc_html( $retailer->business_owner_email ); ?>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Phone Number</div>
            <div class="detail-value"><?php echo esc_html( $retailer->phone_number ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Address</div>
            <div class="detail-value">
                <?php
                // Get country name from geo_countries table
                $country_name = '';
                if ( ! empty( $retailer->address_country ) ) {
                    $country_name = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT country_name FROM geo_countries WHERE country_id = %d",
                            $retailer->address_country
                        )
                    );
                }
                
                $address_parts = array_filter([
                    $retailer->address_street,
                    $retailer->address_city,
                    $retailer->address_state,
                    $retailer->address_postal,
                    $country_name
                ]);
                
                echo esc_html( implode( ', ', $address_parts ) );
                ?>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Onboarding Contact Name</div>
            <div class="detail-value"><?php echo esc_html( $retailer->onboarding_contact_first . ' ' . $retailer->onboarding_contact_last ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Onboarding Contact Phone</div>
            <div class="detail-value"><?php echo esc_html( $retailer->onboarding_contact_phone ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Onboarding Contact Email</div>
            <div class="detail-value detail-email"><?php echo esc_html( $retailer->onboarding_contact_email ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Type of Business</div>
            <div class="detail-value"><?php echo esc_html( $retailer->type_of_business ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Account Billing Email</div>
            <div class="detail-value detail-email"><?php echo esc_html( $retailer->account_billing_email ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Message</div>
            <div class="detail-value" style="height:80px"><?php echo esc_html( $retailer->message ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Agreed to Terms</div>
            <div class="detail-value"><?php echo ( $retailer->agreed_terms ) ? 'Yes' : 'No'; ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Signature</div>
            <div class="detail-value"><img src="<?php echo esc_html( $retailer->signature ); ?>" style="max-width: 400px;"></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Submitted At</div>
            <div class="detail-value"><?php echo esc_html( $retailer->submitted_at ); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Status</div>
            <div class="detail-value"><?php echo ( $retailer->status == 1 ) ? 'Approved' : 'Not Approved'; ?></div>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo admin_url('admin.php?page=retailers'); ?>" class="btn-back plan-btn">Back</a>
        </div>
    </div>
</div>

<style>
.claim-card {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}
.claim-card .detail-row {
    display: flex;
    border: 1px solid #e5e5e5;
}
.claim-card .detail-row:first-of-type {
    border-top: 1px solid #e5e5e5;
}
.claim-card .detail-label {
    width: 35%;
    padding: 12px 15px;
    font-weight: 600;
    background: #f9f9f9;
    border-right: 1px solid #e5e5e5;
}
.claim-card .detail-value {
    flex: 1;
    padding: 12px 15px;
}
.claim-card h3 {
    font-size: 22px;
    font-weight: 600;
}
</style>

</body>