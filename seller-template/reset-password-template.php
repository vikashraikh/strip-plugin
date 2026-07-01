<?php
/**
 * Template Name: Reset Password
 */
 function uim_enqueue_admin_assets()
{
  $plugin_url = plugin_dir_url(__DIR__);
  wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
   wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
   wp_enqueue_script('jquery-1', $plugin_url . 'js/jquery-min.js', array('jquery'), null, true);
  wp_enqueue_style('bootstrap-icon', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_script('coustom-js', $plugin_url . '/js/custom.js',array('jquery'), null, true);
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
if ( isset($_GET['key']) && isset($_GET['login']) ) {
    $user = check_password_reset_key($_GET['key'], $_GET['login']);
    $error = '';
    if ( is_wp_error($user) ) {
        $error = 'Invalid or expired reset link.';
    } elseif ( isset($_POST['new_pass']) ) {
        if ($_POST['new_pass'] !== $_POST['confirm_pass']) {
            $error = 'Passwords do not match.';
        } else {
            reset_password($user, $_POST['new_pass']);
            echo "<p style='color:green;'>Password changed successfully. <a href='" . home_url('/retailer-login/') . "'>Login here</a>.</p>";
            return;
        }
    }
    if ($error) echo "<p style='color:red;'>$error</p>";
    ?>
  <div class="row justify-content-center elite-custom-form">
    <div class="col-md-8 col-lg-5 ">
      <div class=" elite-custom-top mb-4  text-center">
        <h1 class="mb-4">Reset Your Password</h1>
      </div>
      <form method="post" class="form_bg">
          <div class="form-group mb-3 position-relative">
           <input type="password" class="form-control Input-box" name="new_pass" placeholder="New Password" required>
           <i
                         class="fa fa-fw fa-eye position-absolute toggle-password"
                            id="togglePassword"
                            style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                          ></i>
        </div>
        <div class="form-group mb-3 position-relative">
        <input type="password" class="form-control Input-box" name="confirm_pass" placeholder="Confirm Password"
          required>
          <i
                         class="fa fa-fw fa-eye position-absolute toggle-password"
                            id="togglePassword"
                            style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                          ></i>
          </div>
           <div class="text-center">
        <button type="submit"  class="btn btn-primary">Reset Password</button>
  </div>
      </form>
    </div>
  </div>
  <?php
} else {
    echo '<p style="color:red;">Invalid reset request.</p>';
}
?>
</div>
<?php get_footer() ?>