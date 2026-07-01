
<?php
  /**
   * Template Name: Seller Account Template
   */
   function uim_enqueue_admin_assets()
  {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
    wp_enqueue_style('daterangepicker', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css");
     wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
    wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-bundle', $plugin_url . '/js/bootstrap.bundle.min.js' ,array('jquery'), null, true);
  }
  add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');
  get_header();
  ?>
<style>
  .elite-custom-form {
    margin: 8.7em 0;
  }
  .elite-custom-form .Input-box {
    width: 100%;
    max-width: 100%;
    padding: 12px;
    height: auto;
    background: transparent !important;
    border: 1px solid #2a4875;
    color: #000;
  }
  .elite-custom-form .btn,
  .elite-custom-account .btn,
  div#pills-tabContent .btn {
      
    padding: 29px 72px 25px 72px;
    font-family: "Roboto", Sans-serif;
    font-size: 16px;
    font-weight: 500;
    line-height: 11px;
    text-shadow: 0px 0px 0px rgba(0, 0, 0, 0.3);
    color: #FFFFFF;
    border-style: none;
    border-radius: 0px 0px 0px 0px;
    box-shadow: 0px 0px 0px 0px rgba(0, 0, 0, 0.5);
    background: #2a4875;
  }
  .elite-custom-form input,
  .elite-custom-form input::placeholder {
    background-color: Transparent !important;
    color: #000;
  }
  .elite-custom-account input,
  div#pills-tabContent input {
    border: 1px solid #2a4875;
    background-color: Transparent !important;
  }
  .elite-custom-form a,
  .elite-custom-account a {
    color: #2a4875;
    text-decoration: none;
  }
  .elite-custom-top h3 {
    font-size: 28px;
    font-weight: 700;
    line-height: 44px;
    color: #000;
    text-align: center;
    text-transform: capitalize;
  }
  .elite-custom-top p {
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
    color: #000;
    text-align: center;
    text-transform: capitalize;
  }
  .elite-custom-account ul {
    margin: 0;
  }
  .elite-custom-account li a {
    padding: 25px 18px;
  }
  .nav-pills .nav-items.active,
  .nav-pills .show>.nav-link {
    color: #fff;
    background-color: #2a4875 !important;
  }
  .nav-pills .nav-items.active,
  .nav-pills .show>.nav-link {
    color: #2a4875;
    background-color: #fff !important;
  }
  .elite-custom-account li:hover a,
  .elite-custom-account li:active a {
    background: #4b6c9d;
    color: #2a4875;
    border-radius: 0px;
  }
  .elite-custom-account li a.active {
    background: #4b6c9d !important;
    color: #fff !important;
    border-radius: 0px;
  }
  .elite-custom-account li a svg,
  .elite-custom-account li a svg path {
    width: 26px;
    height: 26px;
    fill: #fff;
    margin-right: 8px;
  }
  .elite-custom-account li:hover a {
    color: #fff;
  }
  .elite-custom-account li a {
    color: #fff;
    text-decoration: none;
  }
  .elite-custom-account .edit-profile {
    box-shadow: 0 0 5px;
    padding: 30px;
  }
  ul#pills-tab {
    margin: 0 0 10px;
  }
  ul#pills-tab.nav-pills .nav-link.active,
  .nav-pills .show>.nav-link {
    color: #fff;
    background-color: #2a4875;
  }
  ul#pills-tab.nav-pills .nav-link {
    color: #2a4875;
  }
  .sidebar {
    background-color: #2a4875;
    height: 100%;
    min-height: 100%;
  }
  ul.edit-profile {
    list-style: none;
    margin: 0px;
  }
  ul.edit-profile li {
    margin: 10px 0;
  }
  ul.edit-profile li a {
    list-style: none;
  }
  .form_bg{
   height:100%;
}
</style>
<section role="banner" class="entry-hero page-hero-section entry-hero-layout-standard">
  <div class="entry-hero-container-inner">
    <div class="hero-section-overlay"></div>
    <div class="hero-container site-container">
      <header class="entry-header page-title title-align-inherit title-tablet-align-inherit title-mobile-align-inherit">
        <h1 class="entry-title">Account</h1>
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
            <span class="code4rest-bread-current">Account</span>
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
    <div class="col-md-9 col-lg-9  ">
      <div class="card p-3 pt-0 form_bg">
        <div class="card-body">
          <div class="row mt-3">
              <?php 
                global $wpdb;
                $seller_id = get_current_user_id();
                $warrnties = 'seller_purchaser_info';
                $results = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $warrnties WHERE reseller_id = %d", $seller_id)
                );
                $count = count($results);
              ?>
            <div class="col-md-7 col-lg-7 mb-3">
              <h2>Welcome back, <?php echo $row->onboarding_contact_first?> 
              </h2>
            </div>
            <div class="col-md-5 col-lg-5 d-flex filter-wrapper">
                <label class="filter-width">Date Range: </label>
                <div id="warranty-date-filter-display">
                    <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 48 0c26.5 0 48 21.5 48 48l0 48L0 160l0-48C0 85.5 21.5 64 48 64l48 0 0-32c0-17.7 14.3-32 32-32zM0 192l448 0 0 272c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 192zm64 80l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 400l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z"/></svg>
                </span>
               <input type="text" id="warranty-date-filter" readonly style="cursor: pointer;" />
               </div>
            </div>
            <div class="col-md-4 col-lg-4 text-center">
              <div class="card">
                <div class="card-body">
                  <h2 id="total_warranties"><?php echo $count ?></h2>
                  <h4>Total  <br>Warranties </h4>
                </div>
              </div>
            </div>
          </div>
          <a href="/register-new-warranty/" class="btn btn-primary mt-4">Register New Warranty</a>
        </div>
      </div>
    </div>
  </div>
</div>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<?php get_footer(); ?>