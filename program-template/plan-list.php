<div class="main-wrapper mt-5">
    <h2 class="mb-4 custom-heading">Plans List</h2>
    <div class="add_plan_btn btn-wrap">
 	 <a href="<?php echo'admin.php?page=add_plan&id='.$_GET['id'] ?>" class="btn auto_btn plan-btn">Add Plan</a>
    </div>
    <?php
    global $wpdb;
    $table = 'warranty_plans';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
        
        <table class="tablemanager"  style="width:100%">
           <thead>
            <tr>
                <th class="disableSort">ID</th>
                <th>Name</th>
                <?php /*<th>Description</th>*/?>
                <th>Price</th>
                <th>Status</th>
                <th class="disableFilterBy">Actions</th>
            </tr>
        </thead>
           <tbody>
                <?php foreach ($results as $row): 
                    $edit_url = admin_url('admin.php?page=edit_plan&id=' . $row->id);
                    $view_url = admin_url('admin.php?page=view_plan&id=' . $row->id);
                    $delete_url = admin_url('admin-post.php?action=delete_plan&id=' . $row->id);
                    
                ?>
                    <tr>
                    <td class="prova"><?php echo esc_html( $row->id ); ?></td>
                    <td class="prova product-name"><?php echo esc_html( $row->name ); ?></td>
                    <?php /*<td class="prova"><?php echo esc_html( $row->description ); ?></td>*/?>
                   <td class="prova"><?php $formatted_price = '$' . number_format((float) $row->price, 2, '.', ',');echo esc_html($formatted_price);?></td>
                    <td class="prova">
                        <?php if ( $row->status == 1 ){
                            echo '<img src="/wp-content/plugins/warranty-program/images/icons/publish_img.png">';
                        } else {
                            echo '<img src="/wp-content/plugins/warranty-program/images/icons/unpublish_img.png"';
                        }
                       
                        ?>
                    </td>
                    <td class="prova">
							<a class="edit-svg action_btn" href="<?php echo esc_url( $edit_url ); ?>"><img src="/wp-content/plugins/warranty-program/images/icons/edit.svg" title="Edit"/></a>
							<a class="action_btn" href="<?php echo esc_url( $view_url ); ?>"><img src="/wp-content/plugins/warranty-program/images/icons/view.svg" title="View" /></a>
							<a class="action_btn" href="<?php echo esc_url( $delete_url ); ?>"  onclick="return confirm('Are you sure you want to delete this plan?');"><img src="/wp-content/plugins/warranty-program/images/icons/delete.svg" title="Delete" /></a>
                    </td>
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