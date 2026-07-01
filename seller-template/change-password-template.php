<?php
  /**
   * Template Name: Change Password Template
   */
    function uim_enqueue_admin_assets()
  {
    $plugin_url = plugin_dir_url(__DIR__);
    wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
     wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
     wp_enqueue_style('bootstrap-icon', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-bundle', $plugin_url . '/js/bootstrap.bundle.min.js' ,array('jquery'), null, true);
    wp_enqueue_script('main-js', $plugin_url . '/js/custom.js' ,array('jquery'), null, true);
  }
  add_action('wp_enqueue_scripts', 'uim_enqueue_admin_assets');
  get_header();
 ?>
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
  $user_id = get_current_user_id();
  $user = get_userdata($user_id);
  $messages = [];
  $errors = [];
   if (isset($_POST['change_password']) && wp_verify_nonce($_POST['_nonce_pass'] ?? '', 'change_seller_password')) {
      $current = $_POST['current_password'] ?? '';
      $new = $_POST['new_password'] ?? '';
      $confirm = $_POST['confirm_password'] ?? '';
      if (empty($current) || empty($new) || empty($confirm)) {
          $errors[] = 'All password fields are required.';
      } elseif ($new !== $confirm) {
          $errors[] = 'New password and confirmation do not match.';
      } elseif (!wp_check_password($current, $user->user_pass, $user_id)) {
          $errors[] = 'Current password is incorrect.';
      } else {
          wp_set_password($new, $user_id);
          wp_set_current_user($user_id);
          wp_set_auth_cookie($user_id);
          $messages[] = 'Password changed successfully.';
      }
  }
    ?>
  <section id="change-password" style="margin-bottom:40px;">
    <?php foreach ($messages as $msg): ?>
    <div class="alert alert-success"> <?php echo esc_html($msg); ?> </div>
    <?php endforeach; ?> <?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"> <?php echo esc_html($err); ?> </div>
    <?php endforeach; ?>
    <!-- <h1>Change Password</h1> -->
    <div class="row justify-content-center elite-custom-account ">
      <div class="col-md-3 col-lg-3 ">
        <?php sidebar() ?>
      </div>
      <div class="col-md-9 col-lg-9 form_bg ">
        <div class=" elite-custom-top mb-4">
                <h1>Change Password</h1>
            </div>
        <form method="post" class="">
          <?php wp_nonce_field('change_seller_password', '_nonce_pass'); ?>
          <label for="current_password" class="form-label">Current Password</label>
          <div class="mb-3 form-group position-relative">
            <input type="password" name="current_password" class="form-control" required="">
            <i
                         class="fa fa-fw fa-eye position-absolute toggle-password"
                            id="togglePassword"
                            style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                          ></i>
          </div>
            <label for="new_password" class="form-label">New Password</label>
          <div class="mb-3 form-group position-relative">
            <input type="password" name="new_password" class="form-control" required="">
            <i
                         class="fa fa-fw fa-eye position-absolute toggle-password"
                            id="togglePassword"
                            style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                          ></i>
          </div>
          <label for="confirm_password" class="form-label">Confirm New Password</label>
          <div class="mb-3 form-group position-relative">
            <input type="password" name="confirm_password" class="form-control" required="">
            <i
                         class="fa fa-fw fa-eye position-absolute toggle-password"
                            id="togglePassword"
                            style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                          ></i>
          </div>
            <div class="text-center">
          <button type="submit" name="change_password" class="btn btn-primary ">Change Password</button>
    </div>
        </form>
        </div>
        </div>
        </section>
</div>
<?php get_footer() ?>