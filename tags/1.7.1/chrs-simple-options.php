<?php
/*
Plugin Name:	Simple Theme Options
Plugin URI:		http://wordpress.org/plugins/simple-theme-options/
Description:    Easily add site-wide custom code, such as analytics tracking code, to head and/or footer sections (before the &lt;/head&gt; or &lt;/body&gt;). Additionally manage all your social media links, and display them on your site using shortcodes.
Version:		1.7.1
Author:			Artin Hovhanesian
Author URI:		https://www.chrsinteractive.com/
Text Domain: 	chrssto
License:		GPLv2 or later
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
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Sorry, but you cannot access this page directly.');
}

define('CHRSOP_VERSION', '1.7.1');
define('CHRSOP_REQUIRED_WP_VERSION', '5.0.0');
define('CHRSOP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CHRSOP_PLUGIN_DIR', plugin_dir_path(__FILE__));

add_action('admin_enqueue_scripts', 'chrs_options_style');
add_action('admin_init', 'chrs_theme_options_init');
add_action('admin_menu', 'chrs_theme_options_add');
add_action('admin_head', 'chrs_theme_options_icon');
add_action('wp_head', 'chrs_add_header');
add_action('wp_footer', 'chrs_add_footer');

function chrs_options_style($hook)
{
    wp_enqueue_style('chrs_options', plugin_dir_url(__FILE__) . '/css/styles.css', false, '1.2.0');
    // Load Codemirror Assets.

    if ('toplevel_page_theme_options' === $hook) {
        $cm_settings['codeEditor'] = wp_enqueue_code_editor(['type' => 'text/html']);
        wp_localize_script('code-editor', 'cm_settings', $cm_settings);
        wp_enqueue_style('wp-codemirror');
        wp_enqueue_script('wp-codemirror');
    }
}

function chrs_theme_options_init()
{
    function chrs_html2code( $text ) {
        return '<code>' . htmlspecialchars( $text ) . '</code>';
    }

    $options = get_option('chrs_theme_options');
    add_settings_section(
        'chrs_options_code',
        esc_html__('Global Settings', 'chrssto'),
        '',
        'theme_options'
    );

    add_settings_field(
        'chrs_theme_options_analytics',
        __('Google Analytics ID', 'chrssto'),
        'chrs_field_ga_render',
        'theme_options',
        'chrs_options_code',
        [
            'id'      => 'analytics',
            'value'   => $options['analytics'],
            'example' => esc_html__('Ex: UA-XXXXXXXX-X', 'chrssto'),
        ]
    );
    add_settings_field(
        'chrs_theme_options_fb',
        __('Facebook ID', 'chrssto'),
        'chrs_field_ga_render',
        'theme_options',
        'chrs_options_code',
        [
            'id'      => 'fbpixel',
            'value'   => $options['fbpixel'],
            'example' => esc_html__('Ex: 999999990000000', 'chrssto'),
        ]
    );

    if (current_user_can('unfiltered_html')) {


        add_settings_field(
            'chrs_theme_options_customHeader',
            sprintf(
                esc_html__( 'HEAD (before %s)', 'chrssto' ),
                chrs_html2code( '</head>' )
            ),
            'chrs_textarea_field_render',
            'theme_options',
            'chrs_options_code',
            [
                'id'          => 'customCodeHeader',
                'value'       => $options['customCodeHeader'],
                'description' => sprintf(
                    __( 'Any custom code that needs to be added before %s. <br /><p class="notice"><strong>Please note!</strong> Usage of this plugin should be reserved for tracking codes and other scripts that are safe and trusted. NEVER paste in anything unless you\'re sure you know what it\'s for and it\'s from a verified safe source.', 'chrssto' ),
                    chrs_html2code( '</head>' )
                ),
                'field_class' => 'large-text codeEditor',
            ]
        );
        add_settings_field(
            'chrs_theme_options_customCodeFooter',
            sprintf(
                esc_html__( 'FOOTER (before %s)', 'chrssto' ),
                chrs_html2code( '</body>' )
            ),
            'chrs_textarea_field_render',
            'theme_options',
            'chrs_options_code',
            [
                'id'          => 'customCodeFooter',
                'value'       => $options['customCodeFooter'],
                'description' => sprintf(
                    __( 'Any custom code that needs to be added before %s. <br /><p class="notice"><strong>Please note!</strong> Usage of this plugin should be reserved for tracking codes and other scripts that are safe and trusted. NEVER paste in anything unless you\'re sure you know what it\'s for and it\'s from a verified safe source.', 'chrssto' ),
                    chrs_html2code( '</body>' )
                ),
                'field_class' => 'large-text codeEditor',
            ]
        );
    }
    add_settings_section(
        'chrs_options_social',
        esc_html__('Social Media Profile Links', 'chrssto'),
        '',
        'theme_options'
    );

    $socials = array(
        array(
            'description' => 'Facebook URL',
            'id'          => 'fburl',
            'example'     => 'http://facebook.com/yourprofileurl'
        ),
        array(
            'description' => 'Twitter URL',
            'id'          => 'twurl',
            'example'     => 'http://twitter.com/yourprofileurl'
        ),
        array(
            'description' => 'Instagram URL',
            'id'          => 'igurl',
            'example'     => 'http://instagram.com/yourprofileurl'
        ),
        array(
            'description' => 'WhatsApp URL',
            'id'          => 'waurl',
            'example'     => 'https://wa.me/1234567890'
        ),
        array(
            'description' => 'Google+ URL',
            'id'          => 'gpurl',
            'example'     => 'https://plus.google.com/xxxxxxxxx/posts'
        ),
        array(
            'description' => 'Pinterest URL',
            'id'          => 'pturl',
            'example'     => 'http://www.pinterest.com/yourprofileurl'
        ),
        array(
            'description' => 'Youtube URL',
            'id'          => 'yturl',
            'example'     => 'http://www.youtube.com/user/yourprofileurl'
        ),
        array(
            'description' => 'TikTok URL',
            'id'          => 'tturl',
            'example'     => 'https://www.tiktok.com/@yourprofileurl'
        ),
        array(
            'description' => 'Yelp URL',
            'id'          => 'ypurl',
            'example'     => 'http://www.yelp.com/biz/yourprofileurl'
        ),
        array(
            'description' => 'Snapchat URL',
            'id'          => 'scurl',
            'example'     => 'https://snapchat.com/add/username'
        ),
        array(
            'description' => 'Discord URL',
            'id'          => 'diurl',
            'example'     => 'https://discord.gg/123ABC'
        ),
        array(
            'description' => 'WordPress.com URL',
            'id'          => 'wpurl',
            'example'     => 'http://yourprofile.wordpress.com'
        ),
        array(
            'description' => 'Linkedin URL',
            'id'          => 'liurl',
            'example'     => 'https://www.linkedin.com/in/yourprofile'
        ),
        array(
            'description' => 'Tumblr URL',
            'id'          => 'tburl',
            'example'     => 'https://yourprofile.tumblr.com'
        ),
        array(
            'description' => 'Flickr URL',
            'id'          => 'fkurl',
            'example'     => 'https://www.flickr.com/photos/yourprofile/'
        ),
        array(
            'description' => 'MySpace URL',
            'id'          => 'msurl',
            'example'     => 'https://myspace.com/yourprofile'
        ),
        array(
            'description' => 'Custom 1',
            'id'          => 'ct1url',
            'example'     => 'http://anyurl.com'
        ),
        array(
            'description' => 'Custom 2',
            'id'          => 'ct2url',
            'example'     => 'http://anyurl.com'
        ),
    );
    foreach ($socials as $social) {

        add_settings_field(
            'chrs_theme_options_' . $social['id'],
            __($social['description'], 'chrssto'),
            'chrs_field_social_render',
            'theme_options',
            'chrs_options_social',
            [
                'id'      => $social['id'],
                'value'   => $options[$social['id']],
                'example' => $social['example'],
            ]
        );
    }

    register_setting('chrs_options', 'chrs_theme_options', array(
        'sanitize_callback' => function ($input) use ($socials) {

            $input['customCodeHeader'] = wp_kses_post($input['customCodeHeader']);
            $input['customCodeFooter'] = wp_kses_post($input['customCodeFooter']);
            if (!preg_match('/^(UA|G).*\z/', $input['analytics'])) {
                $input['analytics'] = '';
            }
            $input['analytics'] = sanitize_text_field($input['analytics']);
            foreach ($socials as $social) {
                $input[$social['id']] = esc_url_raw($input[$social['id']]);
            }
            return $input;
        }
    ));
}

function chrs_field_social_render($args)
{
    printf('<input id="chrs_theme_options[%1$s]" type="text" name="chrs_theme_options[%1$s]" value="%2$s" /><br />
									<label for="chrs_theme_options[%1$s]">%3$s</label>',
        $args['id'],
        esc_url($args['value']),
        __($args['example'], 'chrssto')
    );
}

function chrs_field_ga_render($args)
{
    printf('<input id="chrs_theme_options[%1$s]" type="text" name="chrs_theme_options[%1$s]" value="%2$s" /><br />
									<label for="chrs_theme_options[%1$s]">%3$s</label>',
        $args['id'],
        esc_attr($args['value']),
        __($args['example'], 'chrssto')
    );
}

function chrs_textarea_field_render($args)
{
    printf('<textarea id="chrs_theme_options_%1$s" class="%4$s" cols="50" rows="10" name="chrs_theme_options[%1$s]">%2$s</textarea><br />
                    <label for="chrs_theme_options_%1$s">%3$s</label>',
        $args['id'],
        wp_kses_post($args['value']),
        $args['description'],
        //don't use class, cause it will be assigned to <tr> form element also
        $args['field_class']
    );
}

function chrs_theme_options_add()
{
    add_menu_page(__('Theme Options', 'chrssto'), __('Theme Options', 'chrssto'), 'edit_theme_options', 'theme_options', 'chrs_theme_options_do');
}


function chrs_theme_options_do()
{
    global $select_options;
    if (!isset($_REQUEST['settings-updated']))
        $_REQUEST['settings-updated'] = false;


    echo '<div class="chrs-settings-block">';
    if (false !== $_REQUEST['settings-updated']) :
        echo '<div class="updated">';
        echo '<p>';
        _e('Options saved', 'chrssto');
        echo '</p>';
        echo '</div>';
    endif;
    echo '</div>';

    require_once(CHRSOP_PLUGIN_DIR . 'input-global.php');
    require_once(CHRSOP_PLUGIN_DIR . 'instructions.php');

}

require_once(CHRSOP_PLUGIN_DIR . 'shortcodes.php');

function chrs_add_header()
{
    $themeOptions = get_option('chrs_theme_options');
    echo wp_kses_post($themeOptions['customCodeHeader']);
    $fbID = $themeOptions['fbpixel'];
    if(!empty($fbID)){
        echo "<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '" . esc_js($fbID) . "');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height=\"1\" width=\"1\" style=\"display:none\" src=\"" . esc_url("https://www.facebook.com/tr?id=" . $fbID) . "&ev=PageView&noscript=1\" />
</noscript>
<!-- End Facebook Pixel Code -->";
    }
}

function chrs_add_footer()
{
    $themeOptions = get_option('chrs_theme_options');
    echo wp_kses_post($themeOptions['customCodeFooter']);
    $gaID = $themeOptions['analytics'];

    if(!empty($gaID)) {
    echo "<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src='".esc_url("https://www.googletagmanager.com/gtag/js?id=" . $gaID ). "'></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '" . esc_js($gaID) . "');
</script>";
}

}


function chrs_theme_options_icon()
{
    echo '
	<style>
		#adminmenu #toplevel_page_theme_options div.wp-menu-image:before { content: "\f348"; }
	</style>
	';
}

?>
