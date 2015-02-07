<?php
function wii2wx_convert_pp($how) {

	/* New or incompatible in Weaver Xtreme
		'_pp_sidebar_width', '_pp_sitewide-top-widget-area', '_pp_sitewide-bottom-widget-area', '_pp_fi_link', '_pp_fi_location',
		'_show_post_bubble', '_pp_page_layout', '_pp_footer-widget-area', '_header-widget-area', '_footer-widget-area',
		'_page-top-widget-area', '_page-bottom-widget-area',
		'_pp_bgcolor','_pp_color','_pp_bg_fullwidth', '_pp_lr_padding', '_pp_tb_padding', '_pp_margin', '_pp_post_class',
	 */

	echo '<div style="border:1px solid black; padding:1em;background:#F8FFCC;width:95%;margin:1em;">';
	echo "<h2>Convert Per Page / Per Post Settings - {$how}</h2>\n";
/* Reference:
$post relevant fields: $post->ID, $post->post_title

Can set $args to 'post_type' => 'any' to look at them all, including custom posts. Won't hurt
to look at them all, even if Weaver Per Page / Per Post options will never be available.

The idea: scan all posts/pages for those that have some Weaver II Per Page/Post settings.
If they do, the delete the Weaver II setting, and add it back with the new Weaver Xtreme value.

$meta_values = get_post_meta( $post_id, $key, $single ); but only need $meta = get_post_meta( $post-> ID );
delete_post_meta($post_id, $meta_key, $meta_value);  - practical use: delete_post_meta($post_id, $meta_key);
add_post_meta($post_id, $meta_key, $meta_value, $unique);	// practical: add_post_meta($post_id, $meta_key, $meta_value);

 */
	// pages first
	echo "<h3>Posts</h3>\n";
	wii2wx_scan_section('post', $how);
	echo "<h3>Pages</h3>\n";
	wii2wx_scan_section('page', $how);
?>
<p><strong>Notes:</strong> For each setting listed in the report, a leading &#10004; means the setting will be converted when the Convert
button is clicked. A &starf; means there is no equivalent
conversion, or that there is an enhanced version of the setting that requires manual conversion. <strong>Converting</strong> means the conversion is being done. An &times; means the conversion has already been done.</p>
<?php
	echo "</div>\n";
}

function wii2wx_scan_section($what, $how) {

	$pp_map = array(
		'ttw_category' => '_pp_category', 						'ttw_tag' => '_pp_tag',
		'ttw_onepost' => '_pp_onepost', 						'ttw_orderby' => '_pp_orderby',
		'ttw_order' => '_pp_sort_order',  						'ttw_author' => '_pp_author',
		'ttw_posts_per_page' => '_pp_posts_per_page',			'hide_sidebar_primary' => '_pp_primary-widget-area',
		'hide_sidebar_right' => '_pp_secondary-widget-area',	'hide_sidebar_left' => '_pp_secondary-widget-area',
		'top-widget-area' => '_pp_top-widget-area',				'bottom-widget-area' => '_pp_bottom-widget-area',
		'sitewide-top-widget-area' => '_sitewide-top-widget-area', 'sitewide-bottom-widget-area' => '_sitewide-bottom-widget-area',
		'wvr_post_type' => '_pp_post_type',						'ttw-hide-page-title' => '_pp_hide_page_title',
		'ttw-hide-site-title' => '_pp_hide_site_title',			'ttw-hide-menus' => '_pp_hide_menus',
		'ttw-hide-header-image' => '_pp_hide_header_image',		'ttw-hide-footer' => '_pp_hide_footer',
		'ttw-hide-header' => '_pp_hide_header',					'ttw_hide_sticky' => '_pp_hide_sticky',
		'ttw-force-post-full' => '_pp_force_post_full',			'ttw-force-post-excerpt' => '_pp_force_post_excerpt',
		'ttw-show-post-avatar' => '_pp_show_post_avatar',		'ttw-favorite-post' => 'none',
		'ttw_show_extra_areas' => 'none:Additional Top Widget Area',
		'ttw_hide_sidebars' => 'none:Hide Sidebars when this post displayed on Single Post page, new option',
		'bodyclass' => '_pp_bodyclass',							'ttw_show_replace_primary' => '_primary-widget-area',
		'ttw_replace_right' => '_secondary-widget-area',		'ttw_replace_left' => 'none:Left Widget Area no longer supported',
		'hide_top_post_meta' => '_pp_hide_top_post_meta',		'hide_bottom_post_meta' => '_pp_hide_bottom_post_meta',
		'ttw-show-featured' => '_pp_show_featured_img',			'ttw-hide-featured-header' => 'none:Use new featured image settings',
		'ttw-stay-on-page' => '_pp_stay_on_page', 				'ttw-hide-on-menu' => '_pp_hide_on_menu',
		'wvr_show_pp_featured_img' => 'none:Use new featured image options',
		'ttw_hide_pp_infotop' => '_pp_hide_infotop',			'ttw_hide_pp_infobot' => '_pp_hide_infobottom',
		'ttw_show_replace_alternative' => 'none', 				'ttw_per_post_style' => '_pp_post_styles',
		'hide_visual_editor' => '_pp_hide_visual_editor', 		'wvr_masonry_span2' => '_pp_masonry_span2',
		'hide_post_bubble' => 'none:New option is to show bubble',
		'hide_post_title' => '_pp_hide_post_title', 			'post_add_link' => '_pp_post_add_link',
		'hide_post_format_label' => '_pp_hide_post_format_label', 'wvr_page_layout' => '_pp_page_layout',
		'wvr_pwp_type' => '_pp_wvrx_pwp_type', 					'wvr_pwp_cols' => '_pp_wvrx_pwp_cols',
		'ttw-hide-header-widget' => '_pp_header-widget-area', 	'wvr-hide-page-infobar' => '_pp_hide_page_infobar',
		'pp_post_filter' => '_pp_post_filter',					'wvr-hide-on-menu-logged-in' => 'none:No hide on menu if logged in',
		'wvr-hide-on-menu-logged-out' => 'none:Hide on menu if logged in',
		'wvr-hide-on-mobile' => 'none:Hide on mobile',			'wvr_hide_n_posts' => '_pp_hide_n_posts',
		'wvr_fullposts' => '_pp_fullposts',						'replace_horiz_header' => '_pp_header-widget-area',
		'wvr_pwp_masonry' => '_pp_pwp_masonry',					'wvr_pwp_compact' => '_pp_pwp_compact',
		'wvr_pwp_compact_posts' => '_pp_pwp_compact_posts',		'wvr_raw_html' => '_pp_raw_html'
		);

	$args = array('posts_per_page' => -1, 'post_type' => $what, 'post_status' => 'any' );

	$allposts = get_posts($args);
	foreach ($allposts as $post) {
		$id = $post->ID;
		setup_postdata($post);
		$meta = get_post_meta( $id );
		if (!empty($meta)) {
			$type = $post->post_type;
			$title = esc_html($post->post_title);
			$link = esc_url(get_permalink($id));
			$tlink = "<a href='{$link}' alt='Post {$id}' target='_blank'>{$title}</a>";
			$heading = false;
			foreach ($meta as $name => $val_array) {		// old value gets put into $val_array[0]
				if (array_key_exists($name, $pp_map) ) {
					$val = $val_array[0];					// easier to work with
					if ( !$heading ) {
						$heading = true;
						$type_name = 'Post';
						if ($type == 'page')
							$type_name = 'Page';
						echo "<strong>Per {$type_name} settings for <em>{$tlink}</em>:</strong><br /><div style='padding-left:2em;'>\n";
					}
					$to = $pp_map[$name];
					$dummy = array();
					if  ($to == '_pp_post_styles') {	// css, needs mapping
						$val = wii2wx_css_fix($name, $to, $val, $dummy);
					}
					if ( strpos($to, 'none:') !== false ) {
						$to = str_replace('none:','',$to);
						echo "<em>&starf; [{$name}={$val} - {$to}]</em>&nbsp;&nbsp;";
					} elseif (strpos($to, 'none') !== false) {
						echo "<em>&starf; [{$name}={$val} - No equivalent setting]</em>&nbsp;&nbsp;";
					} else {
						$converted = get_post_meta($id, $to, true);
						if ($converted !== false && strlen($converted) > 0) {
							echo "&times; [{$name} already converted to {$to}]&nbsp;&nbsp; ";
						} else {

							// OK - finally to a point where we can make a conversion! ------------------------
							if ( $how == 'convert' ) {
								echo "<strong>Converting</strong>  [{$name}={$val} &rarr; {$to}]&nbsp;&nbsp; ";
								// we will add the new Weaver Xtreme version. If it already exists (like from a previous
								// conversion, or if the user used Xtreme to set some values, we won't add the converted value
								if ( $to == '_pp_page_layout')
									$val = wii2wx_layout($name, $to, $val);
								add_post_meta($id, $to, $val, true);	// add converted meta value - but just once
							} else {
								echo "&#10004; [{$name}={$val} &rarr; {$to}]&nbsp;&nbsp; ";
							}
						}
					}
				}
			}
			if ($heading)
				echo "</div>\n";
			$heading = false;
		} else {
			echo 'No meta: ' . $post->post_title . '<br />';
		}

	}


}
?>
