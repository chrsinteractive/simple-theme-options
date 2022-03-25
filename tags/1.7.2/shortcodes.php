<?php
function chrs_social_links( $atts ) {
	extract( shortcode_atts( array(
		'social_id' => ' ',
		'link_title' => 'Link Title',
	), $atts ) );
	

		$themeOptions = get_option( 'chrs_theme_options' );
		return '<a href="'.esc_url($themeOptions[$social_id]).'">'.$link_title.'</a>';
}
add_shortcode( 'social-link', 'chrs_social_links' );
