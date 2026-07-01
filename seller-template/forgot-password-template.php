<?php
/**
 * Template Name: Forgot Password
 */
 function uim_enqueue_admin_assets()
{
  $plugin_url = plugin_dir_url(__DIR__);
  wp_enqueue_style('uim-bootstrap-css', $plugin_url . '/css/bootstrap.min.css');
   wp_enqueue_style('style-css', $plugin_url . '/css/style.css');
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
              <a href="https://elitewarrantyprogram.idestpro.com/" title="Home" itemprop="url" class="code4rest-bc-home code4rest-bc-home-icon">
                <span>
                  <span class="code4rest-svg-iconset svg-baseline">
                    <svg aria-hidden="true" class="code4rest-svg-icon code4rest-home-svg" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
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
<div class="container ">
<!-- <h1>Reset Your Password</h1> -->
<?php
$message = '';
$error="";
if (isset($_POST['submit_forgot'])) {
    $user_email = sanitize_email($_POST['user_email']);
    $user = get_user_by('email', $user_email);
    if ($user) {
    $reset_key = get_password_reset_key($user);
    if (!is_wp_error($reset_key)) {
        $reset_url = network_site_url("/reset-password?key=$reset_key&login=" . rawurlencode($user->user_login), 'login');
        $subject   = 'Reset Your Password';
        $logo_url = 'https://elitewarrantyprogram.idestpro.com/wp-content/uploads/2025/08/logo1-white.png';
        $message_html = '
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>Reset Password</title>
        </head>
        <body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background-color:#f9f9f9;">
            <div style="max-width:600px;margin:auto;border:1px solid #2a4875;border-radius:10px;overflow:hidden;background:#ffffff;">
                <!-- Logo Section -->
                <div style="text-align:center;background:#2a4875;padding:15px;">
                    <img src="' . esc_url($logo_url) . '" height="80" 
                         alt="Elite Warranty Logo" 
                         style="display:block;margin:0 auto;max-width:200px;height:auto;">
                </div>
                
                <!-- Body Content -->
                <div style="padding:20px;font-size:15px;color:#333;line-height:1.6;">
                    <h2 style="color: #333;">Hi ' . esc_html($user->display_name) . ',</h2>
                    <p style="font-size: 16px; color: #555;">
                      You requested a password reset. Click the button below to choose a new password.
                    </p>
                    <p style="text-align: center;">
                      <a href="' . esc_url($reset_url) . '" style="display: inline-block; padding: 12px 25px; font-size: 16px; color: #fff; background-color: #0073aa; text-decoration: none; border-radius: 5px;">Reset Password</a>
                    </p>
                    <p style="font-size: 14px; color: #999;">
                      If you did not request this, you can ignore this email.
                    </p>
                </div>
                
                <!-- Footer -->
                <div style="background:#f3f3f3;text-align:center;padding:15px;font-size:13px;color:#767676;">
                    <a href="https://elitewarrantyprogram.com/" style="color:#2a4875;text-decoration:none;">
                        www.elitewarrantyprogram.com
                    </a>
                </div>
            </div>
        </body>
        </html>';
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Elite Surface Protection <no-reply@elitewarrantyprogram.com>'
        ];

        add_filter('wp_mail_content_type', function() { return 'text/html'; });

        if (wp_mail($user_email, $subject, $message_html, $headers)) {
            $message = 'Password reset link has been sent to your email';
        } else {
            $error = 'Failed to send the email. Please try again';
        }
    }
}
}

?>
   <div class="row justify-content-center elite-custom-form">
       <?php if (!empty($message)) : ?>
       <div class="alert alert-success" role="alert">
       <?php echo $message; ?>
       </div>
       <?php endif ;?>
       <?php if (!empty($error)) : ?>
       <div class="alert alert-danger" role="alert">
       <?php echo  $error ?>
      </div>
      <?php endif ;?>
        <div class="col-md-8 col-lg-5">
        <div class=" elite-custom-top text-center">
            <h1 class="mb-4">Forget Password</h1>
        </div>
<!-- Forgot Password Form -->
<form method="post" class=" form-group form_bg" >
    <input type="email" class="form-control mb-3 Input-box" name="user_email" placeholder="Enter your email" required>
    <div class="text-center">
      <button type="submit" name="submit_forgot" class="btn btn-primary ">Reset Password</button>
    </div>
</form>
</div>
</div>
<!-- Show success/error message -->

<?php get_footer();  ?>
