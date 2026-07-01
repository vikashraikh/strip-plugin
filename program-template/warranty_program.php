<?php 
global $wpdb;

$siteurl = get_option('siteurl');

$table  = 'seller_purchaser_info';
$total_warranty = $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );

$table1 = 'warranty_claims';
$table2 = 'become_authorized_retailer';
$total_claims   = $wpdb->get_var( "SELECT COUNT(*) FROM {$table1}" );
$activate_retailer = $wpdb->get_var( "SELECT COUNT(*) FROM {$table2} WHERE status = 1" );
$pending_retailer  = $wpdb->get_var( "SELECT COUNT(*) FROM {$table2} WHERE status = 0" );
?>
<link rel="stylesheet" href="<?php echo esc_url($siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Admin Dashboard</h2>
    </div>

    <div class="stats-cards">
         <div class="stat-card">
            <h3><?php echo number_format( (int) $activate_retailer ); ?></h3>
            <p>Active Retailers</p>
        </div>
        <div class="stat-card">
            <h3><?php echo number_format( (int) $pending_retailer ); ?></h3>
            <p>Pending Retailer Activations</p>
        </div>
        <div class="stat-card">
            <h3><?php echo number_format( (int) $total_warranty ); ?></h3>
            <p>Total Warranties Registered</p>
        </div>
        <div class="stat-card">
            <h3><?php echo number_format( (int) $total_claims ); ?></h3>
            <p>Total Claims</p>
        </div>
    </div>

    <div class="grid-buttons">
       <div class="grid-button">
            <a href="<?php echo admin_url('admin.php?page=retailers'); ?>">
                <i class="fas fa-users"></i>
                <span>View Retailers</span>
            </a>
        </div>
      <div class="grid-button">
          <a href="<?php echo admin_url('admin.php?page=warranty_list'); ?>">
        <i class="fas fa-file-alt"></i>
        <span>View Warranties</span>
        </a>
      </div>
      <div class="grid-button">
        <a href="<?php echo admin_url('admin.php?page=claims'); ?>">
        <i class="fas fa-shield-alt"></i>
        <span>View Claims</span>
        </a>
      </div>
      <div class="grid-button">
         <a href="<?php echo admin_url('admin.php?page=transactions'); ?>">
        <i class="fas fa-credit-card"></i>
        <span>Manage Payments</span>
        </a>
      </div>
    </div>
  </div>
