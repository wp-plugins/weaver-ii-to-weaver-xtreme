<?php
/*
Weaver II to Weaver Xtreme - Core functions
*/

//===============================================================
// connect plugin to WP
// Set up Admin
// 1. Add Admin Menu and Admin scripts
// 2. Add runtime scripts

add_action('admin_menu', 'wii2wx_add_page' /* priority: ,6 */);

function wii2wx_add_page() {
    // the 'aspen_switcher' is the ?page= name for forms - use different if not add_theme_page

    //$page = add_theme_page(
	$page = add_management_page(
		'Weaver II to Weaver Xtreme','Weaver Converter','manage_options','wii2wx_tools', 'wii2wx_admin');
    add_action('admin_print_styles-' . $page, 'wii2wx_load_admin_scripts');
}

function wii2wx_admin() {       // This is the Appearance -> Aspen Plus admin menu
    require_once(dirname( __FILE__ ) . '/includes/wii2wx_admin.php'); // NOW - load the admin stuff
    require_once(dirname( __FILE__ ) . '/includes/wii2wx_admin_lib.php'); // NOW - load the plugin admin lib
    wii2wx_admin_page();
}

function wii2wx_load_admin_scripts() {
    // include any style sheets needed for admin side
    wp_enqueue_script('wii2wx_Yetii', wii2wx_plugins_url('/js/yetii',WII2WX_MINIFY.'.js'));

    wp_enqueue_style('wii2wx_admin_Stylesheet', wii2wx_plugins_url('/wii2wx_admin_style', WII2WX_MINIFY . '.css'), array(), WII2WX_VERSION);

    // @@@ wp_enqueue_style ("thickbox");	// @@@@ if we use media browser...
    // @@@ wp_enqueue_script ("thickbox");
}


//---- 2. Add any scripts needed by the plugin runtime

add_action('wp_enqueue_scripts', 'wii2wx_enqueue_scripts' );    // enqueue runtime scripts
function wii2wx_enqueue_scripts() {	// action definition

    //-- Aspen PLus js lib - requires jQuery...

    wp_enqueue_script('wii2wxJSLib', wii2wx_plugins_url('/js/wii2wx_jslib', WII2WX_MINIFY . '.js'),array('jquery'),WII2WX_VERSION);

    // add plugin CSS here, too.

    wp_register_style('wii2wx-style-sheet',wii2wx_plugins_url('wii2wx_style', WII2WX_MINIFY.'.css'),WII2WX_VERSION,'all');
    wp_enqueue_style('wii2wx-style-sheet');
}

// =============================== utility functions =============================

if (!function_exists('wii2wx_plugins_url')) {      // this must be in the plugin root to work right
function wii2wx_plugins_url( $file,$ext ) {
    return plugins_url($file,__FILE__) . $ext;
}
}

require_once(dirname( __FILE__ ) . '/includes/wii2wx_runtime_lib.php'); // NOW - load the plugin

?>
