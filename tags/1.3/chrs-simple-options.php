<?php
/*
Plugin Name: Simple Theme Options
Plugin URI: http://wordpress.org/plugins/simple-theme-options/
Description: With Simple Theme Options plugin your can easily manage your custom theme options such as your social media links. You also have the option to add misc code to the header.php and footer.php files, including your analytics code.
Version: 1.3
Author: Artin Hovhanesian
Author URI: http://www.chrsinteractive.com/
License: GPLv2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( basename( $_SERVER['PHP_SELF'] ) == basename( __FILE__ ) ) {
	die( 'Sorry, but you cannot access this page directly.' );
}

define( 'CHRSOP_VERSION', '1.3' );
define( 'CHRSOP_REQUIRED_WP_VERSION', '3.0' );
define( 'CHRSOP_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define( 'CHRSOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

add_action( 'admin_enqueue_scripts', 'chrs_options_style' );
add_action( 'admin_init', 'chrs_theme_options_init' );
add_action( 'admin_menu', 'chrs_theme_options_add' ); 
add_action( 'admin_head', 'chrs_theme_options_icon' );
add_action( 'wp_head', 'chrs_add_header' );
add_action( 'wp_footer', 'chrs_add_footer' );

function chrs_options_style() {
	wp_enqueue_style( 'chrs_options', plugin_dir_url( __FILE__ ) . '/css/styles.css', false, '1.2.0' );
}

function chrs_theme_options_init(){
	register_setting( 'chrs_options', 'chrs_theme_options');
} 


function chrs_theme_options_add() {
	add_menu_page( __( 'Theme Options', 'chrstheme' ), __( 'Theme Options', 'chrstheme' ), 'edit_theme_options', 'theme_options', 'chrs_theme_options_do' );
}


function chrs_theme_options_do() {
	global $select_options;
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;


		echo '<div class="chrs-settings-block">';
			if ( false !== $_REQUEST['settings-updated'] ) :
			echo '<div class="updated">';
				echo '<p>';
				_e( 'Options saved', 'chrstheme' );
				echo '</p>';
			echo '</div>';
			endif;
		echo '</div>';
		
		require_once( CHRSOP_PLUGIN_DIR . 'input-global.php' );
		require_once( CHRSOP_PLUGIN_DIR . 'instructions.php' );

}

require_once( CHRSOP_PLUGIN_DIR . 'shortcodes.php' );

function chrs_add_header()
{
	$themeOptions = get_option( 'chrs_theme_options' );
	echo $themeOptions['analytics'];
	echo $themeOptions['customCodeHeader'];
}

function chrs_add_footer()
{
	$themeOptions = get_option( 'chrs_theme_options' );
	echo $themeOptions['customCodeFooter'];
}

function chrs_theme_options_icon() {
	echo '
	<style>
		#adminmenu #toplevel_page_theme_options div.wp-menu-image:before { content: "\f348"; }
	</style>
	';
}

?>
