<?php
$claim = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    if (!$claim) {
        wp_die('Warranty claim not found');
    }
   $plugin_url = plugin_dir_url(__DIR__);
?>


<body>
<link rel="stylesheet" href="<?php echo $plugin_url ?>css/bootstrap.min.css"/>
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<div class="container mt-4 custom-container">
    <div class="claim-card">
    <h3 class="text-center mb-4">Claim Details</h3>

    <div class="detail-row">
        <div class="detail-label">Warranty Number</div>
        <div class="detail-value"><?php echo esc_html($claim->plan_number); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Name</div>
        <div class="detail-value"><?php echo esc_html(ucfirst($claim->first_name) . ' ' . ucfirst($claim->last_name)); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Address</div>
        <div class="detail-value">
            <?php
            $address_parts = array_filter([
                $claim->address_line1,
                $claim->address_line2,
                $claim->city,
                $claim->state,
                $claim->zip
            ]);
            echo esc_html(implode(', ', $address_parts));
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Best Contact Phone</div>
        <div class="detail-value"><?php echo esc_html($claim->phone); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Email</div>
        <div class="detail-value detail-email"><?php echo esc_html($claim->email); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Who was your countertop fabricator/installer?</div>
        <div class="detail-value"><?php echo esc_html($claim->fabricator); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">What type of countertop do you have?</div>
        <div class="detail-value"><?php echo esc_html($claim->countertop_type); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">In which room is the damaged countertop?</div>
        <div class="detail-value"><?php echo esc_html($claim->room); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Tell us what the problem is with your countertop</div>
        <div class="detail-value"><?php echo esc_html($claim->problem); ?></div>
    </div>
    <?php if ( !empty($claim->chip_at_sink) && $claim->chip_at_sink !== '' ) { ?>
    <div class="detail-row">
        <div class="detail-label">If you chipped your countertop, did it occur at the sink?</div>
        <div class="detail-value"><?php echo esc_html($claim->chip_at_sink); ?></div>
    </div>
    <?php } ?>
    <div class="detail-row">
        <div class="detail-label">Tell us what happened and how it happened.</div>
        <div class="detail-value"><?php echo esc_html($claim->description); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Did the damage occur during installation or delivery?</div>
        <div class="detail-value"><?php echo esc_html($claim->damage_during_delivery); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Countertop Installation Date?</div>
        <div class="detail-value"><?php echo esc_html($claim->install_date); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Date the Stain or Damage Occurred?</div>
        <div class="detail-value"><?php echo esc_html($claim->damage_date); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Did you attempt to clean or repair it?</div>
        <div class="detail-value"><?php echo esc_html($claim->attempt_clean); ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Damage Photos</div>
        <div class="detail-value">
            <div class="row">
                <?php
                $serialized_photos = $claim->damage_photos;
                $photo_urls = unserialize($serialized_photos);

                if (!empty($photo_urls) && is_array($photo_urls)) {
                    foreach ($photo_urls as $url) {
                        echo '<div class="col-3 mb-2">';
                        echo '<img src="' . esc_url($url) . '" alt="Damage Photo" class="img-thumbnail" style="max-width: 150px;">';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
     <div class="detail-row">
        <div class="detail-label">Submitted At</div>
       <div class="detail-value"><?php if ( ! empty( $claim->submitted_at ) ) {  echo esc_html( date( 'M d, Y', strtotime( $claim->submitted_at ) ) );} ?>
</div>
    </div>
    <div class="text-center mt-4">
        <a href="<?php echo admin_url('admin.php?page=claims'); ?>" class="btn btn-back plan-btn">BACK</a>
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

</div>
</body>
</html>