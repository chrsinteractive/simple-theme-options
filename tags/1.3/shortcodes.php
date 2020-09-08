<?php
function chrs_social_links( $atts ) {
	extract( shortcode_atts( array(
		'social_id' => ' ',
		'link_title' => 'Link Title',
	), $atts ) );
	
	ob_start();
		$themeOptions = get_option( 'chrs_theme_options' );
		echo '<a href="'.$themeOptions[$social_id].'">'.$link_title.'</a>';
	return ob_get_clean();
}
add_shortcode( 'social-link', 'chrs_social_links' );

?>
