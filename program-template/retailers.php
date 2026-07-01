<style>.dataTables_wrapper .dataTables_length select{width: 62px;}</style>
<div class="main-wrapper mt-5">
    <h2 class="mb-4 custom-heading">Retailers Information</h2>
    <div class="add_plan_btn btn-wrap">
 	 <a href="<?php echo'admin.php?page=add_retailer&id='.$_GET['id'] ?>" class="btn auto_btn plan-btn">Add Retailer</a>
    </div>
    <?php
    global $wpdb;
    $table = 'become_authorized_retailer';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
    <?php if($results){?>
       <table class="tablemanager"  style="width:100%">
            <thead>
                <tr>
                    <th class="disableSort">ID</th>
                    <th>Buisiness Name</th>
                    <th>Buisiness Owner</th>
                    <th>Onboarding Contact Email</th>
                    <th>Onboarding Contact Phone</th>
                    <?php /*<th>Address</th>*/?>
                    <th>Type</th>
                    <?php /*<th>Billing Email</th>*/?>
                    <?php /*<th>Products Selected</th>*/?>
                    <th>Status</th>
                    <th class="disableFilterBy">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): 
                    $edit_url = admin_url('admin.php?page=edit_retailer&id=' . $row->id);
                    $view_url = admin_url('admin.php?page=view_retailer&id=' . $row->id);
                    $delete_url = admin_url('admin-post.php?action=delete_retailer&id=' . $row->id);
                    
                ?>
                    <tr>
                    <td class="prova"><?php echo esc_html( $row->id ); ?></td>
                    <td class="prova"><?php echo esc_html( ucfirst($row->business_name )); ?></td>
                    <td class="prova"><?php echo esc_html( ucfirst($row->business_owner_first) . ' ' . ucfirst($row->business_owner_last )); ?></td>
                    <td class="prova"><?php echo esc_html( $row->onboarding_contact_email ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->onboarding_contact_phone ); ?></td>
                     <?php /*<td class="prova">
                        <?php 
                        echo esc_html(
                            $row->address_street . ', ' .
                            $row->address_city . ', ' .
                            $row->address_state . ', ' .
                            $row->address_postal . ', ' .
                            $row->address_country
                        );
                        ?>
                    </td> */?>
                    <td class="prova"><?php echo esc_html( $row->type_of_business ); ?></td>
                    <?php /*<td class="prova"><?php echo esc_html( $row->account_billing_email ); ?></td>
                    <td class="prova">
                        <?php  
                        $saved_product_ids = !empty($row->products_selected)  ? array_map('intval', explode(',', $row->products_selected))  : array();
                        // Fetch plan names only if IDs exist
                        if (!empty($saved_product_ids)) {
                            $plan_ids = implode(',', array_fill(0, count($saved_product_ids), '%d'));
                            $query = $wpdb->prepare(
                                "SELECT name FROM warranty_plans WHERE id IN ($plan_ids)",
                                ...$saved_product_ids
                            );
                            $saved_products = $wpdb->get_results($query);
                        } else {
                            $saved_products = array();
                        }
                        ?>
                        <ol style="display: block; list-style-type: decimal;">
                            <?php if (!empty($saved_products)) : ?>
                                <?php foreach ($saved_products as $product) : ?>
                                    <li style="margin-bottom: 5px;">
                                        <?php echo esc_html($product->name); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </td>*/?>
                    <td class="prova" style="padding-left:20px">
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
							<a class="action_btn" href="<?php echo esc_url( $delete_url ); ?>"  onclick="return confirm('Are you sure you want to delete this retailer?');"><img src="/wp-content/plugins/warranty-program/images/icons/delete.svg" title="Delete" /></a>
		
                    </td>
                </tr>


                <?php endforeach; ?>
            </tbody>
        </table>
        <?php } 
        else{ ?>
        <p>No Data found</p>
      <?php } ?>
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