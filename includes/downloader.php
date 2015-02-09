<?php
// download converted settings file - 2/2/15

	$wp_root = dirname(__FILE__) .'/../../../../';
	if(file_exists($wp_root . 'wp-load.php')) {
		require_once($wp_root . "wp-load.php");
	} else if(file_exists($wp_root . 'wp-config.php')) {
		require_once($wp_root . "wp-config.php");
	} else {
		exit;
	}

	@error_reporting(0);

	$nonce = '';
	$weaverx_fn = '';
	$ext = '';

	if (isset($_GET['_wpnonce']))
		$nonce = $_GET['_wpnonce'];

	if (isset($_GET['_file']))
		$weaverx_fn = $_GET['_file'];

	if (isset($_GET['_ext']))
		$ext = $_GET['_ext'];

	if ( !$nonce || !$weaverx_fn || !$ext ) {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die(__('Sorry - invalid download','weaver-xtreme' /*adm*/));
	}

	if (! wp_verify_nonce($nonce, 'wii2wx_download')) {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die(__('Sorry - download must be initiated from admin panel.','weaver-xtreme' /*adm*/) . ':' . $nonce);
	}

	if (headers_sent()) {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die(__('Headers Sent: The headers have been sent by another plugin - there may be a plugin conflict.','weaver-xtreme' /*adm*/));
	}

	if ( $ext != 'wxplus') {
		$wii2wx_opts = get_option('wii2wx_settings', array());
		//echo '<pre>'; print_r($wii2wx_opts); echo ('</pre>');
		$weaverx_opts['weaverx_base'] = $wii2wx_opts['wx_converted'];


		if ($ext == 'wxt') {
			$weaverx_header = 'WXT-V01.00';
		} else {
			$weaverx_header = 'WXB-V01.00';			/* Save all settings: 10 byte header */
		}

		$weaverx_settings = $weaverx_header . serialize($weaverx_opts); /* serialize full set of options right now */
	} else {
		$wii2wx_opts = get_option('wii2wx_settings', array());
		//echo '<pre>'; print_r($wii2wx_opts); echo ('</pre>');
		$weaverx_opts['header'] = 'WVRX-PLUS1';		// format
		$weaverx_opts['ext'] = $ext;				// the extension
		$weaverx_opts['weaverxplus'] = $wii2wx_opts['wxplus_converted'];

		$weaverx_settings = serialize($weaverx_opts); /* serialize full set of options right now */
	}
	/* $bom = pack("CCC", 0xef, 0xbb, 0xbf);
	if (0 === strncmp($weaverx_settings, $bom, 3)) {
        $weaverx_settings = substr($weaverx_settings, 3);
	} */

	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$weaverx_fn.'.'.$ext);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . strlen($weaverx_settings));
	echo $weaverx_settings;
	exit;
?>
