<?php
  /**
   * Template Name: My Warranties Template
   */
   
   function uim_enqueue_admin_assets()
  {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
    wp_enqueue_style('datatable', $plugin_url . '/css/jquery.dataTables.min.css');
     wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
    wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-bundle', $plugin_url . '/js/bootstrap.bundle.min.js' ,array('jquery'), null, true);
    wp_enqueue_script('datatable-js', $plugin_url . '/js/jquery.dataTables.min.js' ,array('jquery'), null, true);
    wp_enqueue_script('main-js', $plugin_url . '/js/custom.js' ,array('jquery'), null, true);
  }
  add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');
  get_header();
 ?>

<style>
  a.btn.view-detail {
    padding: 10px;
    /* border-radius: 50%; */
  }
</style>
<section role="banner" class="entry-hero page-hero-section entry-hero-layout-standard">
  <div class="entry-hero-container-inner">
    <div class="hero-section-overlay"></div>
    <div class="hero-container site-container">
      <header class="entry-header page-title title-align-inherit title-tablet-align-inherit title-mobile-align-inherit">
        <h1 class="entry-title"><?php echo the_title() ?></h1>
        <nav id="code4rest-breadcrumbs" aria-label="Breadcrumbs" class="code4rest-breadcrumbs">
          <div class="code4rest-breadcrumb-container">
            <span>
              <a href="https://elitewarrantyprogram.idestpro.com/" title="Home" itemprop="url"
                class="code4rest-bc-home code4rest-bc-home-icon">
                <span>
                  <span class="code4rest-svg-iconset svg-baseline">
                    <svg aria-hidden="true" class="code4rest-svg-icon code4rest-home-svg" fill="currentColor"
                      version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                      <title>Home</title>
                      <path d="M9.984 20.016h-4.969v-8.016h-3l9.984-9 9.984 9h-3v8.016h-4.969v-6h-4.031v6z"></path>
                    </svg>
                  </span>
                </span>
              </a>
            </span>
            <span class="bc-delimiter">/</span>
            <span class="code4rest-bread-current"><?php echo the_title() ?></span>
          </div>
        </nav>
      </header>
    </div>
  </div>
</section>
<div class="container mt-5 mb-5">
  <?php 
  if (!is_user_logged_in()) {
      wp_redirect(site_url('/retailer-login/')); 
      exit;
  }
  $paymentstatus = $wpdb->prefix . "warranty_payments";
  $table = 'seller_purchaser_info';
  $plantable = 'warranty_plans'; 
        $seller_id = get_current_user_id();
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT w.*, 
                            p.id AS plan_id, 
                            p.name AS plan_name, 
                            p.price AS price,
                            p.description AS plan_description
                     FROM $table AS w
                     INNER JOIN $plantable AS p ON w.plan_type = p.id
                     WHERE w.reseller_id = %d
                     ORDER BY w.submitted_at DESC",  
                    $seller_id
                )
            );
        ?>

  <div class="row justify-content-center elite-custom-account ">
    <!-- <div class=" elite-custom-top text-center mb-4">
      <h1>My Warranties</h1>
    </div> -->
    <div class="col-md-3 col-lg-3 ">

      <?php sidebar() ?>  

    </div>

    <div class="col-md-9 col-lg-9  form_bg">
         <div class=" elite-custom-top mb-4">
      <h1 >My Warranties</h1>
</div>
      <?php if($results) { ?>
      <table id="mywarranty" class="datatable-custom table-striped table">
        <thead>
          <tr>
            <th scope="col">Purchase Date</th>
            <th scope="col">Warranty Number</th>
            <th scope="col">Name</th>
            <th scope="col">Plan Type</th>
            <th scope="col">View</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            foreach ($results as $result) : ?>
          <tr>
              
            <td scope="row"><?php echo date("m/d/Y", strtotime($result->submitted_at)); ?></td>
            <td scope="row"> <?php echo $result->id ?> </td>
            <td style="text-transform: capitalize;"> <?php echo $result->first_name . '&nbsp' .  $result->last_name ?> </td>
            <td> <?php  echo $result->plan_name ?> </td>
            <td class="text-center"><a href="<?php echo esc_url( add_query_arg( 'warranty_id', $result->id, site_url('/warranty-details') ) ); ?>" class=""><svg width="22px" height="22px" viewBox="0 0 24 24" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12" cy="12" r="3.5" stroke="#2a4875" />
                  <path
                    d="M20.188 10.9343C20.5762 11.4056 20.7703 11.6412 20.7703 12C20.7703 12.3588 20.5762 12.5944 20.188 13.0657C18.7679 14.7899 15.6357 18 12 18C8.36427 18 5.23206 14.7899 3.81197 13.0657C3.42381 12.5944 3.22973 12.3588 3.22973 12C3.22973 11.6412 3.42381 11.4056 3.81197 10.9343C5.23206 9.21014 8.36427 6 12 6C15.6357 6 18.7679 9.21014 20.188 10.9343Z"
                    stroke="#2a4875" />
                </svg> </a>
            </td>
          </tr>

          <?php 
            endforeach ; ?>
        </tbody>
      </table>
      <?php }
      
       else { 
       echo '<h3> No warranty found </h3>';
       }
       
       ?>
    </div>
  </div>
</div>

<?php get_footer() ?>
<script>
$(document).ready(function() {
  var table = $('#mywarranty').DataTable({ 
        select: false,
        "columnDefs": [{
            className: "Name", 
            "targets":[0],
            "visible": true,
            "searchable":false
        }]
    });
});
</script>