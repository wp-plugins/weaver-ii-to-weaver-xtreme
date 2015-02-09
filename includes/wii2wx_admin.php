<?php
/*
Aspen Themeworks - Plugin admin file
*/

function wii2wx_process() {
    // add a nonced form for each needed action
    // TAB 1 Options


	if (wii2wx_submitted('uploadtheme') &&  isset($_POST['uploadit']) && $_POST['uploadit'] == 'yes') {
		if ( wii2wx_loadtheme() ) {
			if ( wii2wx_converttheme() )
				wii2wx_save_msg("Weaver II theme options converted and ready to download to your computer.");
				//@@@@echo '<pre>'; print_r(wii2wx_getopt('wx_converted')); echo '</pre>';
		}
    }

    if (wii2wx_submitted('clear_settings')) {
		wii2wx_delete_all_options();
		wii2wx_save_msg('Previous Conversion Cleared');
    }

	if (wii2wx_submitted('report_perpp')) {
		require(dirname( __FILE__ ) . '/convert_pp.php'); // load the conversion definitions
		wii2wx_convert_pp('report');
		wii2wx_save_msg('Per Page and Per Post conversion report generated.');
    }

	if (wii2wx_submitted('convert_perpp')) {
		require(dirname( __FILE__ ) . '/convert_pp.php'); // load the conversion definitions
		wii2wx_convert_pp('convert');
		wii2wx_save_msg('Per Page and Per Post settings converted.');
    }
}

//==============================================================
// admin page

function wii2wx_admin_page() {
    if ( !current_user_can( 'manage_options' ) )  {
	wp_die('You do not have sufficient permissions to access this page.');
    }

    // process commands
    wii2wx_process();

    // display forms
?>
    <div class="atw-wrap">
	<div id="icon-themes" class="icon32"></div>
	<div style="float:left;padding-top:8px;"><h2>Weaver II to Weaver Xtreme Admin</h2></div>
	<div style="clear:both;"></div>
	<p>
		This tool will non-destructively convert Weaver II to Weaver Xtreme settings. <strong>Please</strong>,
		read the instructions on the "Help" tab before proceeding. While it is safe to directly convert a production site
		directly, it is always safer and less disruptive to visitors if you can first convert a development site.
	</p>
    <div style="clear:both;"></div>

<?php
    wii2wx_tabs_container('generic-tab', 'padding-left:5px;');		// start of tabs definition

	wii2wx_tabs_tab('tab1','Convert');		// define tab - needs id to match later wii2wx_tabs_content + tab name
	wii2wx_tabs_tab('tab2','Help');

	wii2wx_tabs_content('tab1','wii2wx_admin_tab1',true);	// match tab id + name of tab content function 1st one true
	wii2wx_tabs_content('tab2','wii2wx_admin_tab2');

    wii2wx_tabs_end('generic-tab', '1');	// close it all up.
}

//========================================================================
// Tab 1

function wii2wx_admin_tab1() {

	$fname = wii2wx_getopt('filename');
?>
<h3 style="color:blue;">Conversion Options</h3>
<form enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
		<table>
			<tr><td><strong>Upload and convert Weaver II theme settings from .w2t or .w2b file saved on your computer.</strong><br /><br /></td></tr>
<tr><td>
	Since you are converting an existing Weaver II settings file, you don't have to be running on a live site,
	nor will the settings conversion disrupt your current site.
</td></tr>
<?php
		if ( $fname )
			echo "<tr><td><small><strong style='margin-left:10px;'>Current file: <em>{$fname}</em>. You can select and upload a new file to convert a different one.</strong></small><br /></td></tr>";
?>
				<tr valign="top">
						<td>Select .w2t or .w2b file to upload: <input style="border:1px solid black;" name="uploaded" type="file" />
						<input type="hidden" name="uploadit" value="yes" />
						</td>
				</tr>
				<tr><td><span class='submit'><input name="uploadtheme" type="submit" value="Upload and Convert theme/backup" /></span>&nbsp;<strong>Upload and Convert a Weaver II settings file from your computer.</strong></td></tr>
				<tr><td>&nbsp;</td></tr>
		</table>
		<?php wii2wx_nonce_field('uploadtheme'); ?>
</form>


<?php
	$fname = wii2wx_getopt('filename');
	if ( !$fname ) {
?>
<br /><strong style="color:#000088;font-size:150%;">Please select file to convert first.</strong>
<?php
	} else {
		//echo esc_html('dump:' . print_r(wii2wx_getopt('wii_options'), true));
?>
<br /><strong style="color:#000088;font-size:130%;">Download Converted settings from <em><?php echo $fname; ?></em> to your computer.</strong>

<p>You converted settings have been saved in the WordPress database. As long as you can see this message, the
conversions from the listed Weaver II settings file are available for download. By clicking the following Download link,
the converted settings will downloaded to the location of your choice on your own computer.
You can then use the Weaver Xtreme Save/Restore tab to load these converted
settings to your Weaver Xtreme site. <strong>Be sure to save your existing Weaver Xtreme settings first.</strong></p>
<?php
	$nonce = wp_create_nonce('wii2wx_download');
	$time = date('Y-m-d-Hi');
	if (strpos( $fname, '.w2t') !== FALSE ) {
		$dname = str_replace('.w2t','',$fname);
		$ext = 'wxt';
	} else {
		$dname = str_replace('.w2b','',$fname);
		$ext = 'wxb';
	}


	wii2wx_download_link( '<strong>Save Conversion</strong> - Download converted settings to your computer',
		$dname, $ext, $nonce, $time );
?>
<h4>Convert Weaver II Pro shortcode settings to Weaver Xtreme Plus settings</h4>
<p>Clicking the Download button below will create a <em>.wxplus</em> settings file with compatible
Weaver II Pro settings converted for Weaver Xtreme Plus. You can then use the
<em>Appearance &rarr; Xtreme Plus &rarr; X-Plus Save/Restore</em> tab to upload these converted settings
to your Weaver Xtreme Plus site.
</p>
<?php
	if (strpos( $fname, '.w2t') !== FALSE ) {
		$dname = str_replace('.w2t','',$fname);
		$ext = 'wxplus';
	} else {
		$dname = str_replace('.w2b','',$fname);
		$ext = 'wxplus';
	}


	wii2wx_download_link( '<strong>Save Weaver II Pro Shortcode Settings Conversion</strong> - Download converted settings to your computer',
		$dname, $ext, $nonce, $time );
?>
<hr />
<h3>Convert Weaver II [weaver_xxx] Shortcodes to Weaver Xtreme equivalents</h3>
<p>If you've used Weaver II shortcodes (e.g., [weaver_hide_if]), you will likely have the shortcodes
scattered throughout your content. Rather than try to convert these, a new plugin called <em>Weaver Theme
Compatibility</em> will be available soon that will automatically support the old Weaver II shortcodes.
In fact, that plugin will allow you to use most Weaver II, and even Weaver Xtreme shortcodes, with any
other WP theme.</p>
	<hr />
<?php
	}
?>
	<h3>Convert Per Page and Per Post Settings</h3>
	<p>Both Weaver II and Weaver Xtreme support Per Page and Per Post settings. Most of Weaver II Per Page/Post settings
	are supported by Weaver Xtreme, but with different internal names. This option will permanently, but non-destructively, copy your
	Weaver II Per Page/Post settings to equivalent settings used by Weaver Xtreme.
	These new Custom Field settings are permanent, and can't easily be removed from your database.
	The old Weaver II Per Page/Post settings will not be deleted, so you can switch back to using Weaver II if needed.
	You will find that you will probably want to run the Per Page/Post conversion on your production site.
	<strong>But remember</strong>, it is always a very good idea to make a backup of your WP Database first.
	</p>

	<form id="wii2wx_form4" name="wii2wx_form4" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
		<span class="submit"><input type="submit" name="report_perpp" value="Generate Per Page/Post Pre-Conversion Report"/></span>
		-- This generates a report of all Pages and Posts that have Per Page/Post settings that need conversion to Weaver Xtreme.
		<?php wii2wx_nonce_field('report_perpp'); ?>
	</form><br />

	<form id="wii2wx_form4" name="wii2wx_form4" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
		<span class="submit"><input type="submit" name="convert_perpp" value="Convert Per Page/Post Settings"/
		nSubmit="return confirm('Warning: this process will permanently add new values to your Page and Post Custom Field settings, and cannot be undone. You should backup the database first. Are you sure you want to do this now?');"></span>
		-- This action will copy all the Weaver II Per Page and Per Post settings to new Weaver Xtreme values.
		<?php wii2wx_nonce_field('convert_perpp'); ?>
	</form>
	<hr />

<?php
$set_name = '<em>Per Page/Post conversion</em>';
if ($fname)
	$set_name .= ' and <em>' . $fname . '</em>';
?>

<hr />
	<h3>Clear Current Conversion settings</h3>
	<form id="wii2wx_form3" name="wii2wx_form3" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
		<span class="submit"><input type="submit" name="clear_settings" value="Clear Conversion Settings"/></span>
		-- This will clear the conversion settings from <?php echo $set_name; ?>.
<?php wii2wx_nonce_field('clear_settings'); ?>
	</form>
	<hr />
<?php
}

//========================================================================
// Tab 2

function wii2wx_admin_tab2() {

?>
    <h2>Weaver II to Weaver Xtreme Instructions</h2>
	<p>Weaver II and Weaver Xtreme are very different themes. While they share a family history, they are
	as different as any two members of a "real" family. Weaver Xtreme is the newest theme, and has been
	updated to take advantage of the latest in web design technology.</p>
	<p>While these themes are different, they share enough history that it is possible to automatically
	convert many of your Weaver II setting to mostly compatible Weaver Xtreme settings. Please note that
	this conversion is nowhere near 100% complete - but it will convert maybe 90% of your settings. This
	conversion has been designed is such a way that the converted settings will allow you to then take
	advantage of many of Weaver Xtreme's new features without being overly burdened with legacy
	constraints of Weaver II.</p>
	<p>Note that the conversions done by this tool are non-destructive. You will always be able to go
	back to Weaver II and have all your settings intact.</p>
	<h3>Converting Settings</h3>
	<p>Some of the main differences between Weaver II and Weaver Xtreme are in 4 areas: Menus, Sidebars,
	the Header, and Font handling. Mostly, menus will convert quite well, but if you have custom CSS for you menus,
	it will likely have to be redone. </p>
	<p>Weaver Xtreme does not support separate widget areas (Left and Right)
	for two column widget areas - this is accomplished using Columns per widget are in Xtreme. Thus, sidebar
	widget area settings are all set to the Primary or Secondary sidebar areas as appropriate.
	The visual styling of widget areas and widgets is
	largely intact, but you will likely want to manually setup the new layouts. You may have to reorganize
	which widgets go where. This is usually a fairly simple process.
	</p>
	<p>Weaver Xtreme offers far greater
	customization of the Header area, and you may need to recreate your header, especially if you've
	used the header widget area or HTML insertion. Finally, since Weaver Xtreme has
	so much more flexibility in handling fonts than Weaver II, and uses a completely different font
	family stack. The conversion will map the old font
	selections to similar fonts in Xtreme. You may need to manually reset your fonts. Custom
	Google Fonts are not converted.</p>

<ol>
	<li>You should have both Weaver II and Weaver Xtreme installed.</li>
	<li>It is <strong>highly</strong> recommended that you create a backup of your WP DataBase first.</li>
	<li>Using Weaver II's Save/Restore tab, download your settings - probably all settings to a .w2b file on your computer.</li>
	<li>Open Weaver II to Weaver Xtreme from the Dashboard tools tab.</li>
	<li>From the Convert tab, use the Choose File button to select the Weaver II settings file you want to convert.</li>
	<li>Click the "Upload and Convert theme/backup button. This will load the file, and convert it to Weaver Xtreme settings.</li>
	<li>At the top of the refreshed page, you will get a "CONVERSION REPORT". This report is <strong>important</strong>! It contains
	a summary of the incompatible settings, and more importantly, a list of settings that require manual conversion. You might want to
	copy/paste this report to an editor, or even a temporary WP page. If you lose track of this information, simply repeat the
	above conversion process. Nothing is lost.</li>
	<li>Finally, you should download the converted settings to your computer. This will create a .wxt or .wxb file
	that you can now upload to from the Weaver Xtreme Save/Restore tab.</li>
</ol>

<h3>Converting Per Page and Per Post Settings</h3>
<p>
	This converter will also convert your per page and per post settings from Weaver II to Weaver Xtreme.
	This is a two step process. First, generate the report. This will give you a list of the pages that have
	Per Page/Post settings to convert. Then click the Convert button. This conversion is non-destructive - all
	your original Weaver II settings remain intact. A new set of per page/post settings is added that is
	compatible with Weaver Xtreme.
</p>
<p>
	The report (and, in fact, the conversion itself) will show which settings were converted, which might need
	some manual tweaking, and which are not convertible. You can run the converter more than once - it won't create
	duplicates, but will convert any new per page/post settings you might have created while switching back
	to Weaver II.
</p>
<h3>Converting Weaver II Shortcodes</h3>
<p>A new plugin, Weaver Theme Compatibility, will be available soon on WordPress.org. This new plugin will
support most Weaver II shortcodes for <em>any</em> theme, including Weaver Xtreme. This new plugin was
not released at the time this version of this converter version was released.</p>

<h3>More Conversion Information</h3>
<p>There also is a fairly detailed discussion of the conversion process found <a href="//forum.weavertheme.com/discussion/11303/converting-a-weaver-ii-pro-site-to-weaver-xtreme" target="_blank" alt="Conversion Discussion">
<strong>here</strong></a> on our forum.
</p>

	<hr />

<?php
}

//====================================================================
function wii2wx_loadtheme() {
   // upload theme from users computer
	// they've supplied and uploaded a file


	$ok = true;     // no errors so far

	if (isset($_FILES['uploaded']['name']))
		$filename = $_FILES['uploaded']['name'];
	else
		$filename = "";

	if (isset($_FILES['uploaded']['tmp_name'])) {
		$openname = $_FILES['uploaded']['tmp_name'];
	} else {
		$openname = "";
	}

	//Check the file extension
	$check_file = strtolower($filename);
	$pat = '.';                             // PHP version strict checking bug...
	$end = explode($pat, $check_file);
	$ext_check = end($end);


	if ($filename == "") {
		$errors[] = "You didn't select a file to upload.<br />";
		$ok = false;
	}

	if ($ok && $ext_check != 'w2t' && $ext_check != 'w2b'){
		$errors[] = "Theme files must have <em>.w2t</em> or <em>.w2b</em> extension.<br />";
		$ok = false;
	}

	if ($ok) {
		if (!wii2wx_f_exists($openname)) {
			$errors[] = '<strong><em style="color:red;">'.
			 wii2wx_t_('Sorry, there was a problem uploading your file. You may need to check your folder permissions or other server settings.' /*a*/ ).'</em></strong>'.
				"<br />(Trying to use file '$openname')";
			$ok = false;
		}
	}
	if (!$ok) {
		echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">ERROR</em></strong></p><p>';
		foreach($errors as $error){
			echo $error.'<br />';
		}
		echo '</p></div>';
		return false;
	} else {    // OK - read file and save to My Saved Theme
		// $handle has file handle to temp file.
		$contents = wii2wx_f_get_contents($openname);
		// echo 'UPLOAD:' . esc_html($contents);
		wii2wx_setopt('filename', $filename, false);
		wii2wx_setopt('openname', $openname, false);
		wii2wx_setopt('wii_options', $contents, false);
		wii2wx_save_all_options();
	}
	return true;
}

//========================================

function wii2wx_converttheme() {
	$wii = wii2wx_getopt('wii_options');
	if ( ! $wii ) {
		wii2wx_error_msg('No settings to convert.');
		wii2wx_delete_all_options();
		return false;
	}
	$file_type = substr($wii,0,10);
	if ($file_type != 'W2T-V01.00' && $file_type != 'W2B-V01.00') {
		wii2wx_error_msg('Uploaded .w2t or .w2b file wrong format.');
		wii2wx_delete_all_options();
		return false;
	}

	$wii_settings = array();
	$wii_settings = unserialize(substr($wii,10));
?>
<div style="border:1px solid black; padding:1em;background:#F8FFCC;width:95%;margin:1em;"><span style="font-size:120%;font-weight:bold;">
CONVERSION REPORT FOR <?php echo wii2wx_getopt('filename'); ?>
</span> <br />

<?php
// ============= Actual conversion code here...

require(dirname( __FILE__ ) . '/conversions.php'); // load the conversion definitions

	$opts = $wii_settings['weaverii_base'];      // fetch base opts
	$pro_opts = $wii_settings['weaverii_pro'];
	$reportNS = array();
	$reportMC = array();
	$reportNC = array();
	$reportNOT = array();
	$nones = 0;
	$ns = 0;
	$mc = 0;
	$nc = 0;
	$cv = 0;
	global $wii2wx_opts, $wii2wx_wpad_set;
	$wii2wx_wpad_set = false;	// no wrapper padding set...
	$wii2wx_opts = array();


	foreach ( $opts as $opt => $value ) {
		if ( strlen($value) < 1 || $value == 'default')
			continue;
		if ( ! isset( $convert[$opt] )) {
			$report[] = wii2wx_report("Unknown Weaver II option - {$opt}:{$value}",'??');
			//$report[] = wii2wx_report("'{$opt}' =&gt; '{$value}',");
			continue;
		}
		//continue;	// @@@@
		//echo esc_html("* {$opt}={$value}");
		if (isset($convert[$opt])) {
			$to = $convert[$opt];			// the to value
			if (strpos($to, 'none:NS:')  !== FALSE) {
				$to = substr($to, 8 );
				$opt_name = wii2wx_fix_opt_name($opt);
				$reportNS[] = wii2wx_report('Not Supported: ' . $to . " - [{$opt_name} = '{$value}']");
				$ns++;
				continue;
			} elseif (strpos($to, 'none:MC:') !== FALSE ) {
				$to = substr($to, 8 );
				$opt_name = wii2wx_fix_opt_name($opt);
				$reportMC[] = wii2wx_report('Convert Manually: ' . $to. " - [{$opt_name} = '{$value}']");
				$mc++;
				continue;
			} elseif (strpos($to, 'none:`') !== FALSE ) {
				$to = substr($to, 5 );
				$opt_name = wii2wx_fix_opt_name($opt);
				$reportNC[] = wii2wx_report('Incompatibility: ' . $to . " - [{$opt_name} = '{$value}']");
				$nc++;
				continue;
			} elseif (strpos($to, 'none') !== FALSE ) {
				$reportNOT[] = wii2wx_fix_opt_name($opt);
				$nones++;
				continue;
			} elseif (strpos($to, 'admin') !== FALSE ) {
				continue;
			}

			// To here, than have something to convert

			$rules = explode(';',$to);		// split into separate rules
			foreach ($rules as $rule) {
				if ( strpos($rule,'|') !== FALSE) {
					$parts = explode('|',$rule);
					$function = 'wii2wx_' . $parts[1];
					if (function_exists($function)) {
						$conv = $function($opt, $parts[0], $value);
						if ( $conv !== false )
							$wii2wx_opts[$parts[0]] = $conv;
					} else {
						wii2wx_report("Unknown conversion rule: {$rule}", "<strong>ERROR</strong>");
					}
				} else {
					$wii2wx_opts[$rule] = $value;
				}
				$cv++;
			}

			// To here, than have something to convert
			//echo esc_html("*** [{$opt}:{$value}] -> {$to}") . '<br />';
		}
	}

	$report = "Conversion Report\nOriginal Weaver II settings from " . wii2wx_getopt('filename') . "\n\n";

	if (!empty($reportMC)) {
		echo "<h3>Settings that need <em>Manual Conversion</em> to Weaver Xtreme</h3>\n<ul style='margin-left:2em;list-style-type:disc;'>\n";
		foreach ($reportMC as $txt) {
			echo "<li>{$txt}</li>";
			$report .= $txt . "\n";
		}
		echo "</ul>\n";
	} else {
		echo "<h3>No settings need Manual Conversion to Weaver Xtreme</h3>\n";
	}

	if (!empty($reportNS)) {
		echo "<h3>Settings that are <em>Not Supported</em> by Weaver Xtreme</h3>\n<ul style='margin-left:2em;list-style-type:disc;'>\n";
		foreach ($reportNS as $txt) {
			echo "<li>{$txt}</li>";
			$report .= $txt . "\n";
		}
		echo "</ul>\n";
	}

	if (!empty($reportNC)) {
		echo "<h3>Other <em>Incompatible</em> settings with Weaver Xtreme</h3>\n<ul style='margin-left:2em;list-style-type:disc;'>\n";
		foreach ($reportNC as $txt) {
			echo "<li>{$txt}</li>";
			$report .= $txt . "\n";
		}
		echo "</ul>\n";
	}

	if (!empty($reportNOT)) {
		echo "<h4>Obsolete/incompatible settings unable to be converted - mostly sidebar (sb) and mobile options</h4>\n";
		$n = 0;
		foreach ($reportNOT as $txt) {
			$txt = str_replace('Wvr-II: ','',$txt);
			$n++;
			if ( $n > 5) {
				echo '<br />';
				$n = 1;
			}
			echo $txt . '; ';
		}
	}

	if ( !$wii2wx_wpad_set ) {
		// the Weaver II default for these is 10, and borders, etc. kind of depend on this happening.
		$wii2wx_opts['wrapper_padding_B'] = 10;
		$wii2wx_opts['wrapper_padding_L'] = 10;
		$wii2wx_opts['wrapper_padding_R'] = 10;
		$wii2wx_opts['wrapper_padding_T'] = 10;
	}

	if (isset($wii2wx_opts['subtheme_notes']))
		$wii2wx_opts['subtheme_notes'] .= "\n" . $report;
	else
		$wii2wx_opts['subtheme_notes'] = $report;



	echo "<h4>Notes:</h4>Converted settings: {$cv}. Need Manual Conversion: {$mc}. Not supported {$ns}.<br />\n";

	echo "Other settings (mostly sidebar, mobile specific) not converted: <strong>{$nones}.</strong><br />\n";

	echo "This conversion report for basic settings will be included in the converted Weaver Xtreme <em>Advanced Options:Subtheme Notes</em> box.<br /><br />\n";
	wii2wx_setopt('wx_converted',$wii2wx_opts);


	// ---------------------------------- Weaver II Pro to Weaver Xtreme Plus
	/*
	 */
	$xp_social = array (	// social supported by Xtreme
		'month', 'cart', 'codepen', 'digg', 'dribbble', 'dropbox', 'download', 'mail', 'facebook', 'facebook-alt' , 'feed',
		'flickr', 'foursquare', 'github', 'googleplus', 'googleplus-alt', 'info', 'instagram', 'linkedin', 'linkedin-alt',
		'audio', 'path_usepinterest', 'pinterest-alt', 'phone', 'image', 'pocket', 'polldaddy', 'reddit', 'skype', 'spotify',
		'stumbleupon', 'tumblr', 'twitter', 'twitch', 'vimeo', 'wordpress', 'youtube', 'video'
	);
	$mapfrom = array(
		'email', 	'picasa',	'rss',		'podcast'
					);
	$mapto = array (
		'mail',		'image',	'feed',		'audio'

	);
	$social = array();
	$not_social = array();

	$xp = array();

	// we have to fill in the defaults or they will get wiped when settings are uploaded

	$weaverxplus_social_services = array(
	array ('icon'=>'month', 'site'=>'# Enter URL to your calendar' , 'blurb'=>'Our calendar'),
    array ('icon'=>'cart', 'site'=>'# Enter URL of your site\'s shopping cart' , 'blurb'=>'This site\'s Cart'),
    array ('icon'=>'codepen', 'site'=>'codepen.io' , 'blurb'=>'Codepen: An HTML, CSS, and JavaScript code editor in your browser'),
	array ('icon'=>'digg', 'site'=>'digg.com' , 'blurb'=>'Digg: The best news, videos and pictures on the web as voted on by the Digg community'),
	array ('icon'=>'dribbble', 'site'=>'dribbble.com' , 'blurb'=>'Dribbble is show and tell for creatives'),
    array ('icon'=>'dropbox', 'site'=>'dropbox.com' , 'blurb'=>'Dropbox: Your stuff, anywhere'),
    array ('icon'=>'download', 'site'=>'# Enter URL of your site\'s download' , 'blurb'=>'This site\'s download page'),
	array ('icon'=>'mail', 'site'=>'# E-mail link (mailto:you@example.com or url)' , 'blurb'=>'Send Email to this Site\'s Admin'),
	array ('icon'=>'facebook', 'site'=>'facebook.com' , 'blurb'=>'Facebook: social networking'),
    array ('icon'=>'facebook-alt', 'site'=>'facebook.com' , 'blurb'=>'Facebook: social networking'),
	array ('icon'=>'flickr', 'site'=>'www.flickr.com' , 'blurb'=>'flickr: Share photos and video.'),
    array ('icon'=>'feed', 'site'=>'# Enter URL of your site\'s feed' , 'blurb'=>'This site\'s RSS feed'),
	array ('icon'=>'foursquare', 'site'=>'foursquare.com' , 'blurb'=>'Foursquare helps you find the perfect places'),
    array ('icon'=>'github', 'site'=>'github.com' , 'blurb'=>'Github: Build software better, together.'),
    array ('icon'=>'googleplus', 'site'=>'plus.google.com' , 'blurb'=>'Google+: Real-life sharing rethought for the web'),
    array ('icon'=>'googleplus-alt', 'site'=>'plus.google.com' , 'blurb'=>'Google+: Real-life sharing rethought for the web'),
	array ('icon'=>'info', 'site'=>'# Enter URL of your site\'s info' , 'blurb'=>'This site\'s info page'),
    array ('icon'=>'instagram', 'site'=>'instagram.com' , 'blurb'=>"Instagram: Capture and Share the World's Moments"),
	array ('icon'=>'linkedin', 'site'=>'www.linkedin.com' , 'blurb'=>'LinkedIn: Professional contact information'),
    array ('icon'=>'linkedin-alt', 'site'=>'www.linkedin.com' , 'blurb'=>'LinkedIn: Professional contact information'),
	array ('icon'=>'audio', 'site'=>'#Enter address of podcast' , 'blurb'=>'Listen to our podcast'),
    array ('icon'=>'path', 'site'=>'path.com' , 'blurb'=>'Path: Quality Internet Goods'),
    array ('icon'=>'pinterest', 'site'=>'pinterest.com' , 'blurb'=>'Pintrest: All the things that inspire you.'),
    array ('icon'=>'pinterest-alt', 'site'=>'pinterest.com' , 'blurb'=>'Pintrest:All the things that inspire you.'),
    array ('icon'=>'phone', 'site'=>'# Enter URL of your phone link' , 'blurb'=>'Our phone number'),
    array ('icon'=>'image', 'site'=>'# Enter URL of your photo site' , 'blurb'=>'Our Photos'),
    array ('icon'=>'pocket', 'site'=>'getpocket.com' , 'blurb'=>'Pocket: When you find something you want to view later.'),
    array ('icon'=>'polldaddy', 'site'=>'polldaddy.com' , 'blurb'=>'Polldaddy: Surveys your way.'),
	array ('icon'=>'reddit', 'site'=>'www.reddit.com' , 'blurb'=>'reddit: User-generated news links'),
	array ('icon'=>'skype', 'site'=>'www.skype.com' , 'blurb'=>'Skype: Video and phone calling'),
    array ('icon'=>'spotify', 'site'=>'spotify.com' , 'blurb'=>'Spotify: Music for everyone.'),
	array ('icon'=>'stumbleupon', 'site'=>'www.stumbleupon.com' , 'blurb'=>'StumbleUpon: discover the best of the web'),
	array ('icon'=>'tumblr', 'site'=>'www.tumblr.com' , 'blurb'=>'Tumblr: blogging'),
	array ('icon'=>'twitter', 'site'=>'twitter.com' , 'blurb'=>'Twitter'),
    array ('icon'=>'twitch', 'site'=>'twitch.tv' , 'blurb'=>'Twitch is the world\'s leading video platform and community for gamers'),
	array ('icon'=>'vimeo', 'site'=>'vimeo.com' , 'blurb'=>'Vimeo: Video Sharing'),
	array ('icon'=>'wordpress', 'site'=>'www.wordpress.org' , 'blurb'=>'WordPress: blogging'),
	array ('icon'=>'youtube', 'site'=>'youtube.com' , 'blurb'=>'YouTube: video sharing'),
	array ('icon'=>'video', 'site'=>'#Enter your own video link' , 'blurb'=>'Watch our video'),
);
	foreach ($weaverxplus_social_services as $service) {
	   $id = $service['icon'];

	    $xp['social'][$id.'_hover'] = $service['blurb'];
	}

	// $pro_opts = $wii_settings['weaverii_pro'];

	foreach ($pro_opts['social'] as $sopt => $val ) {
		if (strlen($val) > 0) {
			$curopt = explode('_', $sopt);
			$name = str_replace( $mapfrom, $mapto, $curopt[0]);
			//echo "Searching: {$name} - ";
			if ( in_array( $name,$xp_social) ) { // can convert...
				$xp['social']["{$name}_{$curopt[1]}"] = $val;
				if ( $curopt[1] == 'use' )	// count active options
						$social[] = $curopt[0];
			} else if ($curopt[1] == 'use' ) {
				$not_social[] = $curopt[0];		// not converted
			}
		}
	}

	echo "<h3>Weaver II Pro Shortcode Settings</h3>\n<h4>You must use the <em>Download - Xtreme Plus Settings</em> button to save any converted Weaver II Pro shortcode settings.</h4><ul style='margin-left:2em;list-style-type:disc;'>";

	if ( !empty($social) ) {
		echo "<li>These active Weaver II Pro Social settings converted: <small>(Inactive social options may have been converted, too.)</small><br />&nbsp;&nbsp; ";
		foreach ($social as $socname) {
			echo "{$socname}&nbsp; ";
		}
		echo "</li>\n";
	}
	if (!empty($not_social)) {
		echo "<li>These active Weaver II Pro Social settings with no Weaver Xtreme equivalents <strong>not</strong> converted.<br />&nbsp;&nbsp;";
		foreach ($not_social as $not_name) {
			echo "{$not_name}&nbsp; ";
		}
		echo "</li>\n";
	}

	$nbuttons = 0;

	foreach ($pro_opts['buttons'] as $buttons => $button ) {
		if ( strlen($button) > 0 ) {
			$xp['buttons'][$buttons] = $button;
			if ( strpos ($buttons, '_url') !== false )
				$nbuttons++;
		}
	}
	if ($nbuttons > 0)
		echo "<li>Link Buttons converted: {$nbuttons}</li>\n";

	$num_sc = 1;
	$num_sc_conv = 0;

	foreach ( $pro_opts as $option => $val) {
		if (is_array( $val )) {
			continue;	// skip buttons, social buttons
		}
		if ( strlen($val) < 1)
			continue;

		if ($option == 'wvpsc_num_opts') {
			$xp[$option] = $val;
			$num_sc = $val;
		} elseif (strpos($option, 'wvpsc_') !== false) {
			$xp[$option] = $val;
			if (strpos($option, '_id') !== false)
				$num_sc_conv++;
		} elseif ( $option == 'wvr_disclaimer' ) {
			$xp['disclaimer'] = $val;
		} else if ($option == 'wvp_add_social_to_menu') {
			echo "<li>Add social buttons to menu not supported in Weaver Xtreme.</li>";
		}
	}

	if ($num_sc_conv > $num_sc) {
		$xp['wvpsc_num_opts'] = $num_sc_conv;
	}
	if ($num_sc_conv > 0) {
		echo "<li>Shortcoder definitions converted: {$num_sc_conv}</li>";
	}
	if ( isset($xp['wvr_disclaimer']))
		echo "<li>Comment Policy Converted</li>";

?>
	<li>
		<em>Note:</em> Header Gadgets, Slider Menus, and Total CSS are not supported by Weaver Xtreme so are not converted.
		If you weren't using Weaver II Pro, then you can ignore the conversion notes for Weaver II Pro. They won't affect your site.
	</li>
<?php

	wii2wx_setopt('wxplus_converted', $xp);

	echo "</ul></div>\n";
	return true;
}

function wii2wx_report($msg, $lead = '', $echo = false) {
	if ($echo)
		echo "<strong>{$lead}:&nbsp;</strong> " . esc_html($msg) . "<br />\n";
	return $msg;
}

function wii2wx_fix_opt_name($opt) {
	$c = str_replace(array('wii_','_int','_dec'),'',$opt);
	return 'Wvr-II: ' . str_replace('_', ' ', $c);
}

function wii2wx_rounded_corners($old_opt, $new_opt, $val) {
	global $wii2wx_opts;
//echo "<br /><strong>old_opt:{$old_opt} - new_opt:{$new_opt} - val:{$val}<stong><br/>\n";
	if ($old_opt == 'wii_rounded_corners') {
		$areas = array('wrapper_rounded','primary_rounded','secondary_rounded','top_rounded','bottom_rounded',
				'header_rounded','footer_rounded');
		foreach ($areas as $area) {
			$wii2wx_opts[$area] = '-all';
		}
		$wii2wx_opts['m_primary_rounded'] = '-bottom';
		$wii2wx_opts['m_secondary_rounded'] = '-top';
	} else if ($old_opt == 'wii_rounded_corners_content') {
		$wii2wx_opts['content_rounded'] = '-all';
	}
	return false;
}

function wii2wx_font_family($old_opt, $new_opt, $val) {
	//echo "<br /><strong>old_opt:{$old_opt} - new_opt:{$new_opt} - val:{$val}<stong><br/>\n";
	$converts = array(
			'"Helvetica Neue"' => 'sans-serif',
			'Arial' => 'sans-serif',
			'Verdana' => 'verdana',
			'Tahoma' => 'sans-serif',
			'"Arial Black"' => 'arialBlack',
			'"Avant Garde"' => 'sans-serif',
			'"Comic Sans MS"' => 'comicSans',
			'Impact' => 'arialBlack',
			'"Trebuchet MS"' => 'trebuchetMS',
			'"Century Gothic"' => 'sans-serif',
			'"Lucida Grande"' => 'lucidaSans',
			'Univers' => 'sans-serif',
			'"Times New Roman"' => 'serif',
			'"Bitstream Charter"' => 'serif',
			'Georgia' => 'georgia',
			'Palatino' => 'palatino',
			'Bookman' => 'serif',
			'Garamond' => 'garamond',
			'"Courier New"' => 'monospace',
			'"Andale Mono"' => 'consolas',
	);
	$new_font = 'sans-serif';
	foreach ( $converts as $convert => $font ) {
		if (strpos( $val, $convert ) === 0 ) {
			$new_font = $font;
			break;
		}
	}
	return $new_font;
}

function wii2wx_wrapper_pad_set($old_opt, $new_opt, $val) {
	global $wii2wx_wpad_set;
	$wii2wx_wpad_set = true;
	return $val;
}

function wii2wx_shadows($old_opt, $new_opt, $val) {
	return '-3';
}

function wii2wx_fontsize_px($old_opt, $new_opt, $val) {
	return $val + 4;
}

function wii2wx_title_fontsize($old_opt, $new_opt, $val) {	// convert to fontsize_title value
/* titles
 xxl - 2.625
 xl - 2.25
 l - 1.875
 m - 1.5
 s - 1.25
 xs - 1
 xxs - .875
 */
if ( $val >= 260)
		return 'xxl-font-size-title';
	else if ( $val >= 200)
		return 'xl-font-size-title';
	else if ( $val >= 180)
		return 'l-font-size-title';
	else if ( $val >= 150)
		return 'm-font-size-title';
	else if ( $val >= 125)
		return 's-font-size-title';
	else if ( $val >= 100)
		return 'xs-font-size-title';
	else if ( $val >= 70 )
		return 'xxs-font-size-title';
	else
		return 'm-font-size-title';
}

function wii2wx_text_fontsize($old_opt, $new_opt, $val) {	// convert tt fontsize value
/*
 xxs- .625
 xs- .75
 s - .875
 m - 1.0
 l - 1.125
 xl - 1.25
 xxl - 1.5
  */
	if ( $val >= 150)
		return 'xxl-font-size';
	else if ( $val >= 125)
		return 'xl-font-size';
	else if ( $val >= 110)
		return 'l-font-size';
	else if ( $val >= 100)
		return 'm-font-size';
	else if ( $val >= 87)
		return 's-font-size';
	else if ( $val >= 75)
		return 'xs-font-size';
	else if ( $val >= 50 )
		return 'xxs-font-size';
	else
		return 'm-font-size';

}


function wii2wx_layout($old_opt, $new_opt, $val) {
	$layouts = array(
		'default' => 'default',				// default
		'right-1-col' => 'right',    		// Single column sidebar on Right</option>
		'left-1-col' => 'left',    			// >Single column sidebar on Left</option>
		'right-2-col' => 'right',    		// >Double Cols, Right (top wide)</option>
		'left-2-col' => 'left',    			// >Double Cols, Left (top wide)</option>
		'right-2-col-bottom' => 'right',	// >Double Cols, Right (bottom wide)</option>
		'left-2-col-bottom' => 'left',    	// >Double Cols, Left (bottom wide)</option>
		'split' => 'split',    				// >Split - sidebars on Right and Left</option>
		'one-column' => 'one-column',    	// >No sidebars, one column content</option>
	);
	foreach ($layouts as $layout => $new_val) {
		if ( $layout == $val ) {
			if ( $new_val == 'default' &&
				($new_opt == 'layout_default' || $new_opt == 'layout_default_archive' || $new_opt == '_pp_page_layout') ) {
				return 'right';
			}
			return $new_val;
		}
	}
	return 'right';			// fallback
}

function wii2wx_borders($old_opt, $new_opt, $val) {
	global $wii2wx_opts;
	$areas = array('wrapper_border','primary_border','secondary_border','top_border','bottom_border');
	foreach ($areas as $area) {
		$wii2wx_opts[$area] = 'on';
	}
	return false;
}

function wii2wx_fix_container_bg($old_opt, $new_opt, $val) {
	// try to compensate for #main
	global $wii2wx_opts;
	if (isset($wii2wx_opts[$new_opt])) {	// main_bg must have set it already - don't reset if transparent
		if ( $val == 'transparent' )
			return false;
	}
	return $val;
}

function wii2wx_css_fix($old_opt, $new_opt, $val) {
	require(dirname( __FILE__ ) . '/map_css.php'); // load the conversion definitions
	$new_val = str_replace ( $map_css['weaverii'], $map_css['weaverx'], $val);
	return $new_val;
}

function wii2wx_hide($old_opt, $new_opt, $val) {
	// convert hide true/false to hide all
	return 'hide';
}
function wii2wx_post_icons($old_opt, $new_opt, $val) {
	return 'fonticons';
}

function wii2wx_set_current_to_serialized_values($contents)  {
	global $wii2wx_cache;        // need to mess with the cache

	if (substr($contents,0,10) == 'W2T-V01.00')
		$type = 'theme';
	else if (substr($contents,0,10) == 'W2B-V01.00')
		$type = 'backup';
	else
		return wii2wx_alert(wii2wx_t_("Wrong theme file format version" /*a*/ ));  /* simple check for one of ours */
	$restore = array();
	$restore = unserialize(substr($contents,10));

	if (!$restore) return wwii2wx_alert("Unserialize of Weaver II Theme failed");

	$version = wii2wx_getopt('wii_version_id');       // get something to force load

	if ($type == 'theme') {
		// need to clear some settings
		// first, pickup the per-site settings that aren't theme related...
		$new_cache = array();
		foreach (_cache as $key => $val) {
			if ($key[0] == '_') // these are non-theme specific settings
				$new_cache[$key] = $val;        // keep
		}
		$opts = $restore['wii2wx_base'];      // fetch base opts
		wii2wx_delete_all_options();

		foreach ($opts as $key => $val) {
			if ($key[0] != '_')
				wii2wx_setopt($key, $val, false);     // overwrite with saved theme values
		}

		foreach ($new_cache as $key => $val) {  // set the values we need to keep
			wii2wx_setopt($key,$val,false);
		}
	} else if ($type == 'backup') {
		wii2wx_delete_all_options();

		$opts = $restore['wii2wx_base'];      // fetch base opts
		foreach ($opts as $key => $val) {
			wii2wx_setopt($key, $val, false); // overwrite with saved values
		}
		global $wii2wx_pro_opts;
		$wii2wx_pro_opts = false;
		$wii2wx_pro_opts = $restore['weaverii_pro'];
		wii2wx_wpupdate_option('wii2wx_pro',$wii2wx_pro_opts, 'backup');
	}
	wii2wx_setopt('wii_version_id',$version); // keep version, force save of db
	wii2wx_setopt('wii_last_option','WeaverII');
	wii2wx_save_opts('loading theme');        // OK, now we've saved the options, update them in the DB
	return true;
}
?>
