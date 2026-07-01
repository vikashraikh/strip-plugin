<?php
  /**
   * Template Name: Support Template
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
?>

  <div class="row justify-content-center elite-custom-account ">
    <div class="col-md-3 col-lg-3 ">
      <?php sidebar() ?>  
    </div>
    <div class="col-md-9 col-lg-9  form_bg">
       
         <?php if ( have_posts() ) : ?>
              <?php while ( have_posts() ) : the_post(); ?>
               <?php the_content(); ?>
            <?php endwhile; ?>
            <?php endif; ?>
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
            "visible": false,
            "searchable":false
        }]
    });
});
</script>