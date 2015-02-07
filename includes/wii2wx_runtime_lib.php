<?php

// # Aspen SW Globals ==============================================================
$wii2wx_opts_cache = false;	// internal cache for all settings


// ===============================  options =============================

function wii2wx_getopt($opt) {
    global $wii2wx_opts_cache;
    if (!$wii2wx_opts_cache)
        $wii2wx_opts_cache = get_option('wii2wx_settings' ,array());

    if (!isset($wii2wx_opts_cache[$opt]))	// handles changes to data structure
      {
	return false;
      }
    return $wii2wx_opts_cache[$opt];
}

function wii2wx_setopt($opt, $val, $save = true) {
    global $wii2wx_opts_cache;
    if (!$wii2wx_opts_cache)
        $wii2wx_opts_cache = get_option('wii2wx_settings' ,array());

    $wii2wx_opts_cache[$opt] = $val;
    if ($save)
		wii2wx_wpupdate_option('wii2wx_settings',$wii2wx_opts_cache);
}

function wii2wx_save_all_options() {
    global $wii2wx_opts_cache;
    wii2wx_wpupdate_option('wii2wx_settings',$wii2wx_opts_cache);
}

function wii2wx_delete_all_options() {
    global $wii2wx_opts_cache;
    $wii2wx_opts_cache = false;
    if (current_user_can( 'manage_options' ))
		delete_option( 'wii2wx_settings' );
}

function wii2wx_wpupdate_option($name,$opts) {
    if (current_user_can( 'manage_options' )) {
		update_option($name, $opts);
    }
}

// =============================== transient options =============================

if (!function_exists('wii2wx_globals')) {
function wii2wx_globals($glb) {
    return isset($GLOBALS[$glb]) ? $GLOBALS[$glb] : '';
}
}

if (!function_exists('wii2wx_t_set')) {
function wii2wx_t_set($opt, $val) {
    $GLOBALS['aspen_temp_opts'][$opt] = $val;
}
}

if (!function_exists('wii2wx_t_get')) {
function wii2wx_t_get($opt) {
    return isset($GLOBALS['aspen_temp_opts'][$opt]) ? $GLOBALS['aspen_temp_opts'][$opt] : '';
}
}

if (!function_exists('wii2wx_t_clear')) {
function wii2wx_t_clear($opt) {
    unset($GLOBALS['aspen_temp_opts'][$opt]);
}
}

if (!function_exists('wii2wx_t_clear_all')) {
function wii2wx_t_clear_all() {
    unset($GLOBALS['aspen_temp_opts']);
}
}

function wwii2wx_alert($msg) {
	echo "<script> alert('" . $msg . "'); </script>";
	// echo "<h1>*** $msg ***</h1>\n";
}
?>
