<div class="main-wrapper mt-5">
    <h2 class="mb-4 custom-heading">All Transactions</h2>
    <?php
    global $wpdb;
    $table = 'wp_warranty_payments';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
        
        <table class="tablemanager"  style="width:100%">
            <thead>
                <tr>
                    <th class="disableSort">ID</th>
                    <th>Warranty Number</th>
                    <th>Name</th>
                    <th>Protection Plan type</th>
                    <th>Amount</th>
                    <th>Stripe Payment ID</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): 
                    if($row->currency='usd'){ $currency = '$';}
                ?>
                    <tr>
                    <td class="prova"><?php echo esc_html( $row->id ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->warranty_id ); ?></td>
                   <td class="prova"> <?php echo esc_html( ucfirst( $row->user_name ). ' ' . ucfirst($row->user_last_name ) ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->protection_plan_type ); ?></td>
                    <td class="prova"><?php echo esc_html( $currency.$row->amount ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->stripe_payment_id ); ?></td>
                    <td class="prova <?php echo $row->status;?>"><?php echo esc_html( ucfirst($row->status )); ?></td>
                    <td class="prova"><?php echo esc_html( date( 'M j, Y', strtotime( $row->created_at ) ) ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php $siteurl = get_option('siteurl'); ?>
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/warranty-program/css/admin-style.css'; ?>">
<link rel="stylesheet" href="<?php echo $siteurl . '/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css'; ?>">
<script src="<?php echo $siteurl . '/wp-content/plugins/warranty-program/js/tableManager.js'; ?>"></script>
   <script>
	jQuery('.tablemanager').tablemanager({
		// firstSort: [[3,0],[2,0],[1,'asc']],   
		disable: ["last"],
		appendFilterby: true,
		dateFormat: [
			[4, "dd-mm-yyyy"]
		],
		debug: true,
		vocabulary: {
			voc_filter_by: 'Filter By',
			voc_type_here_filter: 'Filter...',
			voc_show_rows: 'Rows Per Page'
		},
		pagination: true,
		showrows: [20, 40, 60, 80, 100, 120, 140, 160, 180],
		disableFilterBy: [1,8]
	});
</script>
</div>