<div class="main-wrapper mt-5">
    <h2 class="mb-4 custom-heading">Claims Information</h2>
    <?php
    global $wpdb;
    $table = 'warranty_claims';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
        
        <table class="tablemanager"  style="width:100%">
            <thead>
                <tr>
                    <th class="disableSort">ID</th>
                    <th>Warranty Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th class="disableFilterBy">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): 
                    $url = admin_url('admin.php?page=view_claim&id=' . $row->id);
                    
                ?>
                    <tr>
                    <td class="prova"><?php echo esc_html( $row->id ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->plan_number ); ?></td>
                    <td class="prova"><?php echo esc_html(ucfirst($row->first_name) . ' ' . ucfirst($row->last_name)); ?></td>
                    <td class="prova"><?php echo esc_html( $row->email ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->phone ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->description ); ?></td>
                    <td class="prova"> <a class="action_btn" href="<?php echo esc_url( $url ); ?>"><img src="/wp-content/plugins/warranty-program/images/icons/view.svg" title="View" /></a></td>
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