<?php
/*
Plugin Name: Peep This
Plugin URI: http://tx-daily.co.za/peep-this
Description: Adds a "Peep This" button to every post and page. Shortens URLs in advance. Customize under Settings > Peep This.
Author: Tx Daily (Seagyn Davis)
Version: 1.01
Author URI: http://www.txdaily.co.za/
Text Domain: peep-this
*/

/*
Peep This is a plugin for WordPress 1.5 - 2.7.1 and WordPress MU.
Copyright 2009  Tx Daily  (email : sdavis@txsolutions.co.za)
Freely released under Version 2 of the GNU General Public License as published
by the Free Software Foundation, or, at your option, any later version.
*/


function pt_option($key) {
	$options = get_option('peep_this_settings'); return $options[$key];
}

function pt_read_file($url) {
	if (ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') ==
	'on' ||	ini_get('allow_url_fopen') == 'On') {
		$file = @file_get_contents($url); if ($file == false) {
			$handle = fopen($url, 'r');
			$file = fread($handle, 4096); fclose($handle);}} else {
		if (function_exists('curl_init')) {$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		$file = curl_exec($ch);	curl_close($ch);}}
	if ($file != false && $file != '') return $file;
}


function peep_this_short_url() {
	global $id; $purl = get_permalink();
	$cached_url = get_post_meta($id, 'peep_this_url', true);
	if($cached_url && $cached_url != 'getnew') return $cached_url;
	else {
		$url = pt_read_file('http://gatorurl.com/api/rest.php?url=' . $purl);		
		if ($cached_url == 'getnew')
		update_post_meta($id, 'peep_this_url', $url, 'getnew');
		else add_post_meta($id, 'peep_this_url', $url, true);
	}
	return $url;
}

function peep_this_trim_title() {
	$title = get_the_title();
	$special = array('&#34;', '&#034;', '&#38;', '&#038;', '&#39;',
	'&#039;', '&#60;', '&#060;', '&#62;', '&#062;', '&#160;', '&#161;',
	'&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;',
	'&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;',
	'&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;',
	'&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;',
	'&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;',
	'&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;',
	'&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;',
	'&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;',
	'&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;',
	'&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;',
	'&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;',
	'&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;',
	'&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;',
	'&#253;', '&#254;', '&#255;', '&#8211;', '&#8212;', '&#8216',
	'&#8217;', '&#8220;', '&#8221;', '&#8230;', '&#8482;', '&#8243;',
	'&amp;', '&gt;', '&lt;', '&quot;', '’', '“', '”');
	$normal = array('"', '"', '&', '&', '\'', '\'', '<', '<', '>', '>',
	' ', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '',
	'®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»',
	'¼', '½', '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É',
	'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×',
	'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å',
	'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó',
	'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ', '--', '--',
	'\'', '\'', '"', '\'\'', '...', '\'', '"', '&', '>', '<', '"', '\'',
	'"', '"');
	$title = str_replace($special, $normal, $title);
	$title = $title . ' ';
	$url_len = '26';
	$len1 = (135 - $url_len); $len2 = ($len1 - 5);
	$len3 = ($len1 + 5); $title = substr($title,0,$len1);
	$title = substr($title,0,strrpos($title,' '));
	if (strlen($title) > $len2 && strlen($title) < $len3)
	$title = $title . ' ...'; $title = urlencode($title);
	return $title;
}

// is_preview doesn't exist in WP 1.5.
function pt_is_preview() {if (function_exists('is_preview')) is_preview();}

function pt_display_limits($item) {
	if (!pt_is_preview()) {
		if (pt_option('pt_limit_to_posts') == 'true') {
			if (pt_option('pt_limit_to_single') == 'true') {
				if (is_single()) return $item;}
			else {if (!is_page()) return $item;}}
		if (pt_option('pt_limit_to_single') == 'true') {
			if (pt_option('pt_limit_to_posts') == 'true') {
				if (is_single()) return $item;}
			else {if (is_single() || is_page()) return $item;}}
		if (pt_option('pt_limit_to_posts') != 'true' &&
		pt_option('pt_limit_to_single') != 'true') return $item;
	}
}

function peep_this_url($peep_text = '') {
	$peep_title = peep_this_trim_title();
	$peep_url = peep_this_short_url();
	$item = 'http://gatorpeeps.com/?url='.$peep_url.'&txt='.$peep_title;
	return pt_display_limits($item);
}

function peep_this() {
	if ($img_class == '') $img_class = 'nothumb';
	$url = peep_this_url($peep_text);
	if ($icon_file == '') {
		if (pt_option('pt_gatorpeeps_icon') == '') $icon_file = 'pt-gatorpeeps.png';
		else $icon_file = pt_option('pt_gatorpeeps_icon');
	}
	$icon = get_option('siteurl').'/wp-content/plugins/peep-this/icons/'.$icon_file;
	$item = '<a class="peep-this" href="'.$url.'" title="Peep on Gatorpeeps"><img class="nothumb" src="'.
			$icon.'" alt="Peep on Gatorpeeps" border="0" /></a>';
	return pt_display_limits($item);
}

function insert_peep_this($content) {
	global $id;
	$peep_this_hide = get_post_meta($id, 'peep_this_hide', true);
	if ($peep_this_hide && $peep_this_hide != 'false')
		$content = $content;
	else {
		$align = pt_option('pt_alignment'); 
		if ($align == '')	$align = 'left';
		$p = '<p align="' . $align . '">'; $p2 = '</p>';
		$content .= pt_display_limits($p);
		$content .= peep_this() . '&nbsp;';
		$content .= pt_display_limits($p2);
	}
	return $content;
}

function peep_this_css() {
	echo '<style type="text/css">a.peep-this{text-decoration:none;}</style>';
}

function update_pt_options() {
	if(isset($_REQUEST['pt'])) $new_options = $_REQUEST['pt'];
	$booleans = array('pt_alignment', 'pt_limit_to_single', 'pt_limit_to_posts', 'pt_twitter_icon');
	foreach($booleans as $key)
		$new_options[$key] = $new_options[$key] ? 'true' : 'false';
	update_option('peep_this_settings', $new_options);
	echo '<br /><div id="message" class="updated fade"><p>' .
		__('Peep This options saved!', 'peep-this') . '</p></div>';}


function pt_image_selection() {
	$l = '/wp-content/plugins/peep-this/icons/pt-';
	$checked = ' checked="checked"'; 
	$u = get_option('siteurl');
	$y = '.png" /></p><p><input type="radio" name="pt[pt_';
	$z = '.png" /> <input type="radio" name="pt[pt_';
	echo '<p><input type="radio" name="pt[pt_gatorpeeps_icon]" value="pt-gatorpeeps-big.png"';	
	if (pt_option('pt_gatorpeeps_icon') == 'pt-gatorpeeps-big.png' || pt_option('pt_gatorpeeps_icon') == '') echo $checked;
	echo ' /> <img src="' . $u . $l . 'gatorpeeps-big.png" alt="pt-gatorpeeps-big' . $z .
	'gatorpeeps_icon]" value="pt-gatorpeeps-medium.png"';
	if (pt_option('pt_gatorpeeps_icon') == 'pt-gatorpeeps-medium.png')
	echo $checked;
	echo ' /> <img src="' . $u . $l . 'gatorpeeps-medium.png" alt="pt-' .
	'gatorpeeps-medium' . $z . 'gatorpeeps_icon]" value="pt-' . gatorpeeps . '-small.png"';
	if (pt_option('pt_gatorpeeps_icon') == 'pt-gatorpeeps-small.png')
	echo $checked; 
	echo ' /> <img src="' . $u . $l . 'gatorpeeps-small.png" alt="pt-' .
	'gatorpeeps-small' . $z . 'gatorpeeps_icon]" value="pt-gatorpeeps-text.png"';
	if (pt_option('pt_gatorpeeps_icon') == 'pt-gatorpeeps-text.png')
	echo $checked;
	echo ' /> <img src="' . $u . $l . 'gatorpeeps-text.png" alt="pt-' .
	'gatorpeeps-text.png" />';
}

function print_pt_form() {
	if ($_REQUEST['reset'])
		echo '<br /><div id="message" class="updated fade"><p>' .
		__('Peep This options reset!', 'peep-this') . '</p></div>';
	$s = ' selected="selected"';
	$v = ' checked="checked"'; $w = pt_option('pt_url_www'); global $wpdb;
	$count1 = number_format($wpdb->get_var("SELECT COUNT(*)
		FROM $wpdb->posts WHERE post_status = 'publish'"));
	$count2 = number_format($wpdb->get_var("SELECT COUNT(*) FROM
		$wpdb->postmeta WHERE meta_key = 'peep_this_url' AND
		meta_value != 'getnew'"));
	if ($count2 > $count1) $count2 = $count1;
	echo '<script type="text/javascript">var lastDiv = ""; function ' .
	'showDiv(divName) {if (lastDiv) {document.getElementById(lastDiv).' .
	'className = "hiddenDiv";} if (divName && document.getElementById' .
	'(divName)) {document.getElementById(divName).className = "visible' .
	'Div"; lastDiv = divName;}}</script><style type="text/css">label.t ' .
	'{margin-top:5px;display:block;width:130px;padding:0;float:left;} ' .
	'.hiddenDiv {display:none;} .visibleDiv {display:block;}</style><div' .
	' class="wrap"><h2>' . __('Peep This Options', 'peep-this') .
	'</h2><p>'; if ((ini_get('allow_url_fopen') == 0 ||
	ini_get('allow_url_fopen') == 'off' || ini_get('allow_url_fopen') ==
	'Off') && !function_exists('curl_init')) echo '<strong><font color="' .
	'red">' . __('Allow_url_fopen and curl are disabled in your PHP ' .
	'configuration.</font><br />All URLs will be served locally, ' .
	'regardless of your chosen URL service.<br />To fix this, try ' .
	'adding these lines to your <a href="http://www.washington.edu/' .
	'computing/web/publishing/php-ini.html">php.ini file</a>:<br /> `' .
	'extension = curl.so` and `allow_url_fopen = on`.' . '</strong>',
	'peep-this') . '</font></p><p>';
	echo '</p><form id="peep-this" name="peep-this" method="post" ' .
	'action="">'; if (function_exists('wp_nonce_field'))
		wp_nonce_field('update-options');
	echo '<p><label class=' . '"t" for="pt[pt_alignment]">' . __('Alignment:', 'peep-this') .
	'</label><select name="pt[pt_alignment]" id="pt[pt_alignment]">' .
	'<option value="left"'; if (pt_option('pt_alignment') == 'left')
	echo $s; echo '>' . __('Left', 'peep-this') . '</option><option ' .
	'value="right"'; if (pt_option('pt_alignment') == 'right') echo $s;
	echo '>' . __('Right', 'peep-this') . '</option><option value="' .
	'center"'; if (pt_option('pt_alignment') == 'center') echo $s;
	echo '>' . __('Center', 'peep-this') . '&nbsp;</option></select>' .
	'</p><p><label><input type="checkbox" name="pt[pt_limit_to_single]"';
	if (pt_option('pt_limit_to_single') == 'true') echo $v; echo ' /> ' .
	__('Only show Peep This when viewing single posts or pages',
	'peep-this') . '</label></p><p><label><input type="checkbox" name=' .
	'"pt[pt_limit_to_posts]"';
	if (pt_option('pt_limit_to_posts') == 'true') echo $v; echo ' /> ' .
	__('Hide Peep This on pages', 'peep-this') .
	'</label></p>';	pt_image_selection(); echo '<input type="hidden"' .
	' name="action" value="update" /><input type="hidden" name="page_' .
	'options" value="pt[pt_alignment],pt[pt_limit_to_single],pt[pt_limit_to_posts],pt[pt_gatorpeeps_icon]" />' .
	'<p class="submit"><input type="submit" name="submit" value="' .
	__('Save Options', 'peep-this') . '" /> <input type="submit" name="' .
	'reset" value="' . __('Reset Options', 'peep-this') . '" onclick="' .
	'return confirm (\'' . __('Are you sure you want to reset Peep This' .
	' to its default settings?', 'peep-this') . '\');" /></p></form><p>'.
	__('Visit the <a href="http://txdaily.co.za">Tx Daily</a> page for support.', 'peep-this').
	'</p></div>';
}

function peep_this_install() {
	$add_options = array('pt_alignment' => 'left',
	'pt_gatorpeeps_icon' => 'pt-gatorpeeps-big.png', 'pt_limit_to_single' =>
	'false','pt_limit_to_posts' => 'false');
	foreach($add_options as $key => $value) {
		if ($old = get_option($key)) {$add_options[$key] = $old;
			delete_option($key);}}
	if (get_option('peep_this_settings') == '')
		add_option('peep_this_settings', $add_options);
	delete_option('pt_add_title'); delete_option('pt_big_icon');
	delete_option('pt_icon'); delete_option('pt_small_icon');
}

function peep_this_add_options() {
	if (function_exists('add_options_page')) {
		add_options_page(__('Peep This Options', 'peep-this'),
			__('Peep This', 'peep-this'), 8,
			__FILE__, 'peep_this_options');}
}

function peep_this_options() {
	if ($_REQUEST['submit']) {
		update_pt_options();}
	if ($_REQUEST['reset']) {
		delete_option('peep_this_settings'); global_flush_pt_cache();
		peep_this_install();} print_pt_form();
}

// Sets one cached URL to "getnew". peep_this_short_url() respawns.
function flush_pt_cache($post_id) {
	$cached_pt_url = get_post_meta($post_id, 'peep_this_url', true);
	if($cached_pt_url && $cached_pt_url != 'getnew') {
	update_post_meta($post_id, 'peep_this_url', 'getnew');}
}


// Deletes the cached URL when you delete one post.
function delete_pt_cache() {
	global $id; delete_post_meta($id, 'peep_this_url');
}


// Sets every cached URL to "getnew". For permalink / URL service changes.
function global_flush_pt_cache() {
	global $wpdb; $wpdb->query("UPDATE $wpdb->postmeta
	SET meta_value = 'getnew' WHERE meta_key = 'peep_this_url'");
}


// Deletes every cached URL. Triggered upon deactivation.
function global_delete_pt_cache() {
	global $wpdb;
	// Careful here.
	$wpdb->query("DELETE FROM $wpdb->postmeta " .
	"WHERE meta_key = 'peep_this_url'");
}

add_action('admin_menu', 'peep_this_add_options');
add_action('publish_post', 'flush_pt_cache');
add_action('publish_future_post', 'flush_pt_cache');
add_action('save_post', 'flush_pt_cache');
add_action('edit_post', 'flush_pt_cache');
add_action('delete_post', 'delete_pt_cache');
add_action('generate_rewrite_rules', 'global_flush_pt_cache');
add_action('wp_head', 'peep_this_css');
add_filter('the_content', 'insert_peep_this');

if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__, 'peep_this_install');}

if (function_exists('register_deactivation_hook')) {
	register_deactivation_hook(__FILE__, 'global_delete_pt_cache');}
?>