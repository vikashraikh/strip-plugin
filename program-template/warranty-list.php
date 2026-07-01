<style>.dataTables_wrapper .dataTables_length select{width: 62px;}</style>
<div class="main-wrapper mt-5">
    <h2 class="mb-4 custom-heading">Warrantys Information</h2>
    <?php
    global $wpdb;
    $table = 'seller_purchaser_info';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
    <?php if($results){?>
    <!--<div class="filter-bar" style="margin-bottom:15px;">-->
    <!--    <label for="filter_day">Day:</label>-->
    <!--    <input type="date" id="filter_day">-->
    
    <!--    <label for="filter_month">Month:</label>-->
    <!--    <input type="month" id="filter_month">-->
    
    <!--    <button id="filter_btn" class="button button-primary">Filter</button>-->
    <!--</div>-->

       <table class="tablemanager"  style="width:100%">
            <thead>
                <tr>
                    <th class="disableSort">Warranty Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Plan Type</th>
                    <th class="disableFilterBy">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): 
                    $url = admin_url('admin.php?page=view_warranty&id=' . $row->id);
                    $saved_product_ids = !empty($row->plan_type)  ? array_map('intval', explode(',', $row->plan_type))  : array();
                ?>
                    <tr>
                    <td class="prova"><?php echo esc_html( $row->id ); ?></td>
                   <td class="prova"> <?php echo esc_html(ucwords(trim($row->first_name . ' ' . $row->last_name))); ?></td>
                    <td class="prova"><?php echo esc_html( $row->email ); ?></td>
                    <td class="prova"><?php echo esc_html( $row->phone ); ?></td>
                    <td class="prova">
                        <?php if (!empty($saved_product_ids)) {
                            $plan_ids = implode(',', array_fill(0, count($saved_product_ids), '%d'));
                            $query = $wpdb->prepare(
                                "SELECT name FROM warranty_plans WHERE id IN ($plan_ids)",
                                ...$saved_product_ids
                            );
                            $saved_products = $wpdb->get_results($query);
                        } else {
                            $saved_products = array();
                        } ?>
                        <ol style="display: block; list-style-type: none;margin-left: 5px;">
                            <?php if (!empty($saved_products)) : ?>
                                <?php foreach ($saved_products as $product) : ?>
                                    <li style="margin-bottom: 5px;">
                                        <?php echo esc_html($product->name); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </td>
                    <td class="prova"> <a class="action_btn" href="<?php echo esc_url( $url ); ?>"><img src="/wp-content/plugins/warranty-program/images/icons/view.svg" title="View" /></a></td>
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
        jQuery(document).ready(function($){
        
            $('#filter_btn').on('click', function(e){
                e.preventDefault();
        
                var day   = $('#filter_day').val();   // YYYY-MM-DD
                var month = $('#filter_month').val(); // YYYY-MM
        
                $.ajax({
                    url: ajaxurl, // WP admin AJAX URL
                    type: "POST",
                    data: {
                        action: "filter_warranty_by_date",
                        day: day,
                        month: month
                    },
                    beforeSend: function(){
                        $('tbody').html('<tr><td colspan="6">Loading...</td></tr>');
                    },
                    success: function(response){
                        if(response.success){
                            $('tbody').html(response.data.html);
                        } else {
                            $('tbody').html('<tr><td colspan="6">'+response.data.message+'</td></tr>');
                        }
                    }
                });
            });
        
        });
        </script>

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