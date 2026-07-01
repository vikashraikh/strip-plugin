<?php
/**
 * Template Name: Seller Login Template
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
if (!defined('ABSPATH')) exit;
// ---- Email verification logic ----
function handle_email_verification() {
    if (!isset($_GET['verify_email'], $_GET['uid'], $_GET['token'])) {
        return;
    }
    $user_id = intval($_GET['uid']);
    $token   = sanitize_text_field($_GET['token']);
    $saved_token = get_user_meta($user_id, 'email_verification_token', true);
    $expires     = get_user_meta($user_id, 'email_verification_expires', true);
    if (!$saved_token || $token !== $saved_token) {
        echo '<div class="alert alert-danger">Invalid or expired verification link.</div>';
        return;
    }
    if ($expires && time() > intval($expires)) {
        echo '<div class="alert alert-danger">Verification link has expired.</div>';
        return;
    }
    // Mark verified
    update_user_meta($user_id, 'email_verified', 1);
    delete_user_meta($user_id, 'email_verification_token');
    delete_user_meta($user_id, 'email_verification_expires');
    echo '<div class="alert alert-success">Email verified! You can now <a href="' . esc_url(home_url('/seller-login/')) . '">login</a>.</div>';
}
ob_start(); 
handle_email_verification();
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seller_login'])) {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = wp_remote_post(
        "https://www.google.com/recaptcha/api/siteverify",
        array(
            'body' => array(
                'secret'   => 'YOUR_RECAPTCHA_KEY',
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        )
    );

    $response_body = wp_remote_retrieve_body( $response );
    $result = json_decode( $response_body, true );
 if ( isset($result['success']) && $result['success'] ) {
    $email    = sanitize_email($_POST['user_name'] ?? '');
    $password = $_POST['password_user'] ?? '';
    if (empty($email) || empty($password)) {
        $login_error = 'Email and password are required.';
    } elseif (!is_email($email)) {
        $login_error = 'Please enter a valid email address.';
    } else {
        $user_obj = get_user_by('email', $email);
        if (!$user_obj) {
            $login_error = 'No account found with that email address.';
        } else {
            $creds = [
                'user_login'    => $user_obj->user_login, 
                'user_password' => $password,
                'remember'      => true,
            ];
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                $login_error = 'The password you entered is incorrect. Please try again.';
            } else {
                if (in_array('seller', (array)$user->roles, true)) {
                    $verified = get_user_meta($user->ID, 'email_verified', true);
                    if (empty($verified)) {
                        wp_logout();
                        $login_error = 'Your email is not verified. Please check your inbox.';
                    } else {
                        wp_safe_redirect(home_url('/retailer-account/'));
                        exit;
                    }
                } else {
                    wp_safe_redirect(home_url('/retailer-account/'));
                    exit;
                }
            }
        }
    }
 } else {
        // ❌ reCAPTCHA failed
        $login_error = "❌ reCAPTCHA verification failed. Please try again.";
    }
}
get_header();
?>
<section role="banner" class="entry-hero page-hero-section entry-hero-layout-standard">
    <div class="entry-hero-container-inner">
        <div class="hero-section-overlay"></div>
        <div class="hero-container site-container">
            <header
                class="entry-header page-title title-align-inherit title-tablet-align-inherit title-mobile-align-inherit">
                <h1 class="entry-title">Login</h1>
                <nav id="code4rest-breadcrumbs" aria-label="Breadcrumbs" class="code4rest-breadcrumbs">
                    <div class="code4rest-breadcrumb-container">
                        <span>
                            <a href="<?php echo site_url(); ?>" title="Home" itemprop="url"
                                class="code4rest-bc-home code4rest-bc-home-icon"><span><span
                                        class="code4rest-svg-iconset svg-baseline">
                                        <svg aria-hidden="true" class="code4rest-svg-icon code4rest-home-svg"
                                            fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24">
                                            <title>Home</title>
                                            <path
                                                d="M9.984 20.016h-4.969v-8.016h-3l9.984-9 9.984 9h-3v8.016h-4.969v-6h-4.031v6z">
                                            </path>
                                        </svg></span></span></a></span> <span class="bc-delimiter">/</span> <span
                            class="code4rest-bread-current">Login</span></div>
                </nav>
            </header>
        </div>
    </div>
</section>
<div class="container mt-5 mb-5">
    <?php
    echo ob_get_clean();
     ?>
    <div class="row justify-content-center elite-custom-form">
        <div class="col-md-8 col-lg-5">
            <div class=" elite-custom-top text-center">
                <h1 >Login</h1>
                <p><span style="text-transform: capitalize;">Sign in with Email</span></p>
            </div>
            <form method="post" novalidate class="form_bg">
                <div class="form-group mb-4">
                    <input type="text" class="form-control Input-box" name="user_name" placeholder="Email"
                        required>
                </div>
                <div class="form-group mb-3 position-relative">
                    <input type="password" class="form-control Input-box" name="password_user" placeholder="Password"
                        required>
                        <i
                         class="fa fa-fw fa-eye position-absolute toggle-password"
                            id="togglePassword"
                            style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"
                          ></i>
                </div>
                <div class="form-group mb-3 d-md-flex">
                 <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_KEY"></div>
                </div>
                <div class="form-group mb-3 d-md-flex loginfooter">
                    <div class="w-50">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input mr-1" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">&nbsp; Remember me</label>
                        </div>
                    </div>
                    <div class="w-50 text-end float-end ">
                        <a href="/forgot-password/">Forgot Password</a>
                    </div>
                </div>
                <div class="text-center">
                 <button type="submit" class="btn btn-primary " name="seller_login">Sign In</button>
                </div>
            </form>
            <br>
        <?php if (!empty($login_error)) : ?>
            <div class="alert alert-danger text-center"><?php echo $login_error;?></div>
        <?php endif; ?>
        <br>
              <!-- Load reCAPTCHA script -->
           <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        </div>
        </div>
    </div>
    <?php get_footer(); ?>