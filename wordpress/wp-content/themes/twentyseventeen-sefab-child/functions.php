<?php
function my_theme_enqeue_styles()
{

    $parent_style = 'parent-style';

    wp_enqueue_script("jquery");

    wp_enqueue_script('bootstrap-script', get_stylesheet_directory_uri() . '/resources/bootstrap/js/bootstrap.js');
    wp_enqueue_style('bootstrap-style', get_stylesheet_directory_uri() . '/resources/bootstrap/css/bootstrap.css');

    wp_deregister_style('bs4');
    wp_enqueue_style('bs4', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css', false, null, 'all');
    wp_deregister_script('js4');
    wp_enqueue_script('js4', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', false, null, true);

    wp_enqueue_script('sefab-theme-script', get_stylesheet_directory_uri() . '/resources/scripts/script.js');

    wp_enqueue_script('formhelpers-phone', get_stylesheet_directory_uri() . '/resources/scripts/bootstrap-formhelpers-phone.js');
    wp_enqueue_script('formhelpers-phone.format', get_stylesheet_directory_uri() . '/resources/scripts/bootstrap-formhelpers-phone.format.js');

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array($parent_style), wp_get_theme()->get('Version'));

    if (is_page('login')) {
        wp_enqueue_style('child-style-login', get_stylesheet_directory_uri() . '/resources/styles/page-login-style.css');
        wp_enqueue_script('child-script-login', get_stylesheet_directory_uri() . '/resources/scripts/page-login-script.js');
    }
}
add_action('wp_enqueue_scripts', 'my_theme_enqeue_styles');

function my_theme_enqeue_admin_styles($hook)
{
    if ($hook === 'user-new.php') {
        wp_enqueue_script('admin-script', get_stylesheet_directory_uri() . '/resources/scripts/admin-script.js');
    }

    wp_enqueue_script('formhelpers-phone', get_stylesheet_directory_uri() . '/resources/scripts/bootstrap-formhelpers-phone.js');
    wp_enqueue_script('formhelpers-phone.format', get_stylesheet_directory_uri() . '/resources/scripts/bootstrap-formhelpers-phone.format.js');
    wp_enqueue_style('child-style-test', get_stylesheet_directory_uri() . '/admin-style.css');

    wp_enqueue_style('alertify-style', get_stylesheet_directory_uri() . '/assets/alertify/css/alertify.css');
    wp_enqueue_style('default-style', get_stylesheet_directory_uri() . '/assets/alertify/css/themes/default.css');
    wp_enqueue_script('alertify-script', get_stylesheet_directory_uri() . '/assets/alertify/alertify.js');
}
add_action('admin_enqueue_scripts', 'my_theme_enqeue_admin_styles');

/**
 * Custom Login
 */
/* Main Redirection to the login page */
function redirect_login_page()
{
    $login_page = home_url('/login/');
    $page_viewed = basename($_SERVER['REQUEST_URI']);

    if ($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    }
}
add_action('init', 'redirect_login_page');

/* Redirect when login failed */
function custom_login_failed()
{
    $login_page = home_url('/login/');
    wp_redirect($login_page . '?login=failed');
    exit;
}
add_action('wp_login_failed', 'custom_login_failed');

/* Redirect when fields are empty */
function verify_user_pass($user, $username, $password)
{
    $login_page = home_url('/login/');
    if ($username === '' || $password === '') {
        wp_redirect($login_page . '?login=empty');
        exit;
    }
}
add_filter('authenticate', 'verify_user_pass', 1, 3);

/* On logout */
function logout_redirect()
{
    $login_page = home_url('/login/');
    wp_redirect($login_page . '?login=false');
    exit;
}
add_action('wp_logout', 'logout_redirect');

function redirect_to_specific_page()
{
    if ((!is_page('login') && !is_page('privacy-policy')) && !is_user_logged_in()) {
        $login_page = home_url('/login/');
        wp_redirect($login_page, 301);
        exit;
    }
}
add_action('template_redirect', 'redirect_to_specific_page');

function new_registration_save($user_id)
{

    if (isset($_POST['user_login'])) {

        $user_login = $_POST['user_login'];

        $clean_user = substr(preg_replace('/-+/', '', $user_login), -10);
        global $wpdb;

        $wpdb->update($wpdb->users, array('user_login' => $clean_user), array('ID' => $user_id));

    }
}

add_action('user_register', 'new_registration_save', 10, 1);

function order_posts_by_mod_date($orderby)
{
    if (is_home() || is_archive() || is_feed()) {
        $orderby = "post_modified_gmt DESC";
    }
    return $orderby;
}
add_filter('posts_orderby', 'order_posts_by_mod_date', 999);
