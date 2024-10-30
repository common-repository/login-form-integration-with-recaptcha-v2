<?php
/*
Plugin Name: Login Form Integration with Recaptcha V2
Plugin URI: 
Description: Adding Google Recaptcha V2 in Login Form
Author: Rajat Meshram
Version: 1.0
Author URI: https://rajatmeshram.in
Text Domain: login-form-integration-with-recaptcha-v2
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once(__DIR__.'/admin-menu.php');

$recpv2_site_key = sanitize_text_field(get_option('site_key'));
$recpv2_secret_key = sanitize_text_field(get_option('secret_key'));

if (empty($recpv2_site_key) && empty($recpv2_secret_key)) {
    function recpv2_admin_notice_error() {
        $class = 'notice notice-error';
        $message = __('Recaptcha v2 Site key and secret key are empty.', 'login-form-integration-with-recaptcha-v2');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
    add_action('admin_notices', 'recpv2_admin_notice_error');
}

function recpv2_login_style() {
    wp_register_script('login-recaptcha', 'https://www.google.com/recaptcha/api.js', false, null);
    wp_enqueue_script('login-recaptcha');
    echo "<style>p.submit, p.forgetmenot {margin-top: 10px!important;}.login form{width: 303px;} div#login_error {width: 322px;}</style>";
}
add_action('login_enqueue_scripts', 'recpv2_login_style');

function recpv2_add_recaptcha_on_login_page() {
    $recpv2_site_key = sanitize_text_field(get_option('site_key'));
    echo '<div class="g-recaptcha brochure__form__captcha" data-sitekey="' . esc_attr($recpv2_site_key) . '"></div>';
}
add_action('login_form', 'recpv2_add_recaptcha_on_login_page');

function recpv2_captcha_login_check($user, $password) {
    $recpv2_secret_key = sanitize_text_field(get_option('secret_key'));
    if (!empty($_POST['g-recaptcha-response'])) {
        $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        $captcha = sanitize_text_field($_POST['g-recaptcha-response']);
        
        $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $recpv2_secret_key . '&response=' . $captcha . '&remoteip=' . $ip);
        
        if (is_wp_error($response)) {
            return new WP_Error('Captcha Error', __('Captcha verification failed!', 'login-form-integration-with-recaptcha-v2'));
        }

        $valid = json_decode(wp_remote_retrieve_body($response), true);

        if ($valid['success'] == true) {
            return $user;
        } else {
            return new WP_Error('Captcha Invalid', __('Captcha Invalid! Please check the captcha!', 'login-form-integration-with-recaptcha-v2'));
        }
    } else {
        return new WP_Error('Captcha Invalid', __('Captcha Invalid! Please check the captcha!', 'login-form-integration-with-recaptcha-v2'));
    }
}
if (!empty($recpv2_site_key) && !empty($recpv2_secret_key)) {
add_action('wp_authenticate_user', 'recpv2_captcha_login_check', 10, 2);
}
?>
