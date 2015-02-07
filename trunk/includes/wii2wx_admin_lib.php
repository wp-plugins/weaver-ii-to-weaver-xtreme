<?php
//===================================================================
//
// Aspen plugin library - should be same from plugin to plugin, but keep in each theme top file
// all wrapped up in function_exists to avoid duplication if other aspen plugins active..
//

/*
    ================= nonce helpers =====================
*/
function wii2wx_submitted($submit_name) {
    // do a nonce check for each form submit button
    // pairs 1:1 with wii2wx_nonce
    $nonce_act = $submit_name.'_act';
    $nonce_name = $submit_name.'_nonce';

    if (isset($_POST[$submit_name])) {
	if (isset($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name],$nonce_act)) {
	    return true;
	} else {
	    die("WARNING: invalid form submit detected ($submit_name). Probably caused by session time-out, or, rarely, a failed security check.
                Please contact AspenThemeWorks.com if you continue to receive this message.");
	}
    } else {
	return false;
    }
}

function wii2wx_nonce_field($submit_name,$echo = true) {
    // pairs 1:1 with sumbitted
    // will be one for each form submit button

    return wp_nonce_field($submit_name.'_act',$submit_name.'_nonce',$echo);
}

function wii2wx_save_msg($msg) {
    echo '<div id="message" class="updated fade" style="width:80%;"><p><strong>' . $msg .
	    '</strong></p></div>';
}
function wii2wx_error_msg($msg) {
    echo '<div id="message" class="updated fade" style="background:#F88; width:80%;"><p><strong>' . $msg .
	    '</strong></p></div>';
}

/*
    ================= form helpers =====================
*/

function wii2wx_form_checkbox($id, $desc, $br = '<br />') {
?>
    <div style = "display:inline-block;padding-left:2.5em;text-indent:-1.7em;"><label><input type="checkbox" name="<?php echo $id ?>" id="<?php echo $id; ?>"
        <?php checked(wii2wx_getopt($id) ); ?> >&nbsp;
<?php   echo $desc . '</label></div>' . $br . "\n";
}

/*
    ================= yetii tabs lib =====================
*/
if (!function_exists('wii2wx_tabs_end')) {
function wii2wx_tabs_end($id = 'tab-container', $instance = '0') {
?>
	</div>
    </div>
    <script type="text/javascript">
	var tabber<?php echo $instance;?> = new Yetii({
	id: '<?php echo $id; ?>',
	tabclass: 'atw-tab',
	persist: true
	});
    </script>
<?php
}
}

if (!function_exists('wii2wx_tabs_container')) {
function wii2wx_tabs_container($id='tab-container', $style='') {
    $s = '';
    if ($style != '')
	$style = ' style="' . $style . '"';
    echo '<div id="' . $id . '-wrap"' . $style . ">\n";
    echo '<div id="' . $id . '" class="yetii-w">' . "\n";
    echo '<ul id="' . $id . '-nav" class="yetii">' . "\n";
}
}

if (!function_exists('wii2wx_tabs_tab')) {
function wii2wx_tabs_tab($tab_id, $tab_name) {
    // Define a tab. Each tab has a unique id + name for the tab
    echo '<li><a href="#' . $tab_id . '" title="' . $tab_name . '">' . $tab_name . '</a></li>' . "\n";
}
}

if (!function_exists('wii2wx_tabs_content')) {
    function wii2wx_tabs_content($tab_id, $tab_function, $first = false) {
    if ($first)
	echo "</ul>\n";		// end the
    echo '<div id="' . $tab_id . '" class="atw-tab">';
    $tab_function();
    echo "\n</div> <!-- #$tab_id -->\n";
}
}

/*
    ================= general helpers =====================
*/

function wii2wx_help_link( $ref, $label) {

    $t_dir = wii2wx_plugins_url('/help/' . $ref, '');
    $icon = wii2wx_plugins_url('/help/help.png','');
    $pp_help =  '<a href="' . $t_dir . '" target="_blank" title="' . $label . '">'
		. '<img class="entry-cat-img" src="' . $icon . '" style="position:relative; top:4px; padding-left:4px;" title="Click for help" alt="Click for help" /></a>';
    echo $pp_help ;
}

// ========= file
function wii2wx_f_exists($fn) {
	// this one must use native PHP version since it is used at theme runtime as well as admin
	if ($fn == 'php://output' || $fn == 'echo')
		return true;
	if (function_exists('aspentw_f_exists'))
		return aspentw_f_exists( $fn );
	return @file_exists($fn);
}

function wii2wx_f_get_contents($fn) {
	if ($fn == 'php://output' || $fn == 'echo')
		return '';
	if (function_exists('aspentw_f_get_contents'))
		return aspentw_f_get_contents( $fn );
	return implode('',file($fn));       // works if no newlines in the file...
}

function wii2wx_download_link($desc, $dname, $ext, $nonce, $time) {
	$downloader = plugins_url() . '/weaver-ii-to-weaver-xtreme/includes/downloader.php';
	$download_img_path = plugins_url() . '/weaver-ii-to-weaver-xtreme/includes/images/download.png';
	if (!$dname)
		$dname = 'weaverx-converted';
	$filename = $dname . '-' . $time;
	$href = $downloader . "?_wpnonce={$nonce}&_ext={$ext}&_file={$filename}";
?>
	<a style="text-decoration: none;" href="<?php echo esc_url($href); ?>">
	<span class="download-link">
		<img src="<?php echo esc_url($download_img_path); ?>" />
	<?php echo '<strong>Download</strong>'; echo '</span></a> - ';
    echo $desc; echo ' &nbsp;';
	_e('Save as:', 'weaver-xtreme' /*adm*/); echo ' ' . $filename . '.' . $ext . "<br /><br />\n";
}

?>
