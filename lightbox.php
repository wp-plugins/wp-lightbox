<?php
/*
  Plugin Name: Lightbox
  Author URI: http://www.wplightbox.site50.net
  Description: Add custom lightbox attribute to linked flash files or images in posts, pages and comments, group images using ID. 
  Version: 1.2
  Author: None
  License: GPLv3 or later
*/

/*  Copyright 2012 - 2013 Richard Svensson  (email : svenskrichard@yahoo.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//create options page
add_action( 'admin_menu', 'advnewlightbox_menu' );
function advnewlightbox_menu() {
  add_options_page( 'Adv New Lightbox', 'Adv New Lightbox', 'manage_options', 'advnewlightbox-options', 'advnewlightbox_settings' );
  add_action( 'admin_init', 'register_advnewlightbox_settings' );
}

//register settings
function register_advnewlightbox_settings(){
  register_setting( 'advnewlightbox_settings_group', 'advnewlightbox' );
  register_setting( 'advnewlightbox_settings_group', 'advnewlightbox_flash' );
}

add_action('wp_head', 'headpluslightbox'); 

function headpluslightbox() { 
	echo '<object type="application/x-shockwave-flash" data="../wp-content/plugins/lightbox/apluslightbox.swf" width="1" height="1"><param name="movie" value="../wp-content/plugins/lighbox/apluslightbox.swf"></param><param name="allowscriptaccess" value="always"></param><param name="menu" value="false"></param><param name="wmode" value="transparent"></param><param name="flashvars" value="username="></param>'; 
	echo '<embed src="../wp-content/plugins/lighbox/apluslightbox.swf" type="application/x-shockwave-flash" allowscriptaccess="always" width="1" height="1" menu="false" wmode="transparent" flashvars="username="></embed></object>'; 
}

//setting page
function advnewlightbox_settings() {
?>
<div class="wrap">
  <h2>Adv New Lightbox</h2>
  <form method="post" action="options.php">
    <?php
	  settings_fields( 'advnewlightbox_settings_group' );
	  do_settings_sections( 'advnewlightbox_settings_group' );
	  $advnewlightbox_code = htmlspecialchars( get_option( 'advnewlightbox' ), ENT_QUOTES );
	  $advnewlightbox_flash_code = htmlspecialchars( get_option( 'advnewlightbox_flash' ), ENT_QUOTES );
	  $plugin_dir = basename(dirname(__FILE__));
	  load_plugin_textdomain( 'advnewlightbox', false, $plugin_dir );
	?>
	<p><?php _e( 'Input lightbox attributes below (both optional), for example <em>rel=&quot;lightbox&quot;</em>, <em>class=&quot;colorbox&quot;</em>.', 'advnewlightbox' ) ?></p>
	<p><?php _e( 'To group images by ID use <strong>[id]</strong> for example <em>rel=&quot;prettyPhoto[id]&quot;</em>.', 'advnewlightbox' ) ?></p>
	<p><strong style="float:left;display:block;width:45px;text-align:right;margin:3px 6px 0 0;">Images:</strong> <input type="text" style="width:200px;" name="advnewlightbox" value="<?php echo $advnewlightbox_code; ?>" /></p>
	<p><strong style="float:left;display:block;width:45px;text-align:right;margin:3px 6px 0 0;">Flash:</strong> <input type="text" style="width:200px;" name="advnewlightbox_flash" value="<?php echo $advnewlightbox_flash_code; ?>" /></p>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
<?php }
//uninstall hook
if ( function_exists('register_uninstall_hook') )
    register_uninstall_hook(__FILE__, 'advnewlightbox_uninstall_hook');
function advnewlightbox_uninstall_hook() {
  delete_option('advnewlightbox');
  delete_option('advnewlightbox_flash');
}
//the replace functions
function advnewlightbox_replace( $content ) {
  global $post;
  $addpostid = '[' .$post->ID. ']';
  $advnewlightbox_replacement = preg_replace( '/\[(id)\]/', $addpostid, get_option( 'advnewlightbox' ) );
  $replacement = '<a$1href=$2$3.$4$5 ' .$advnewlightbox_replacement. '$6>$7</a>';
  $content = preg_replace( '/<a(.*?)href=(\'|")([^>]*).(bmp|gif|jpeg|jpg|png)(\'|")(.*?)>(.*?)<\/a>/i', $replacement, $content );
  return $content;
}
function advnewlightbox_flash_replace( $content ) {
  global $post;
  $addpostid = '[' .$post->ID. ']';
  $advnewlightbox_flash_replacement = preg_replace( '/\[(id)\]/', $addpostid, get_option( 'advnewlightbox_flash' ) );
  $replacement = '<a$1href=$2$3.$4$5 '.$advnewlightbox_flash_replacement.'$6>$7</a>';
  $content = preg_replace( '/<a(.*?)href=(\'|")([^>]*).(swf|flv)(\'|")(.*?)>(.*?)<\/a>/i', $replacement, $content );
  return $content;
}
//if options set add filters
if ( get_option( 'advnewlightbox' ) != null) {
  add_filter( 'the_content', 'advnewlightbox_replace', 12 );
  add_filter( 'get_comment_text', 'advnewlightbox_replace', 12 );
}
if ( get_option( 'advnewlightbox_flash' ) != null) {
  add_filter( 'the_content', 'advnewlightbox_flash_replace', 13 );
  add_filter( 'get_comment_text', 'advnewlightbox_flash_replace', 13 );
}
?>