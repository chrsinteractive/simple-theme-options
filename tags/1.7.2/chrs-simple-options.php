<?php
/*
Plugin Name:	Simple Tracking
Plugin URI:		http://wordpress.org/plugins/simple-theme-options/
Description:    Easily add site-wide tracking codes and conversion pixels. Additionally manage all your social media links, and display them on your site using shortcodes.
Version:		1.7.2
Author:			CHRS Interactive
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

define('CHRSOP_VERSION', '1.7.2');
define('CHRSOP_REQUIRED_WP_VERSION', '5.0.0');
define('CHRSOP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CHRSOP_PLUGIN_DIR', plugin_dir_path(__FILE__));

add_action('admin_enqueue_scripts', 'chrssto_options_style');
add_action('admin_init', 'chrssto_theme_options_init');
add_action('admin_menu', 'chrssto_theme_options_add');
add_action('wp_head', 'chrssto_add_header');
add_action('wp_body_open', 'chrssto_add_body');
add_action('wp_footer', 'chrssto_add_footer');

function chrssto_options_style($hook)
{
    wp_enqueue_style('chrs_options', plugin_dir_url(__FILE__) . '/css/styles.css', '1.7.2');
    // Load Codemirror Assets.

    if ('toplevel_page_theme_options' === $hook) {
        $cm_settings['codeEditor'] = wp_enqueue_code_editor(['type' => 'text/html']);
        wp_localize_script('code-editor', 'cm_settings', $cm_settings);
        wp_enqueue_style('wp-codemirror');
        wp_enqueue_script('wp-codemirror');
    }
}

function chrssto_theme_options_init()
{
    function chrs_html2code( $text ) {
        return '<code>' . htmlspecialchars( $text ) . '</code>';
    }

    function chrssto_info_box()
    {
        ?>
        <div id="chrssto_info_box">
            <p><?php esc_html_e('The following are the most popular tracking codes being used on the web. If you have any other tracking code requests, that you think will be a good addition to the plugin, please feel free to contact us', 'chrssto') ?>.</p>
            <p><a href="mailto:hello@chrsinteractive.com" target="_blank"><strong>hello@chrsinteractive.com</strong></a></p>
        </div>

        <?php
    }

    $options = get_option('chrs_theme_options');
    add_settings_section(
        'chrssto_options_code',
        esc_html__('Tracking Codes/Pixels', 'chrssto'),
        '',
        'simple_tracking'
    );

    add_settings_field(
        'chrs_theme_options_analytics',
        __('Google Analytics ID', 'chrssto'),
        'chrs_field_pixel_render',
        'simple_tracking',
        'chrssto_options_code',
        [
            'id'      => 'analytics',
            'value'   => $options['analytics'],
            'example' => esc_html__('Ex: UA-XXXXXXXX-X or G-XXXXXXXXXX', 'chrssto'). ' - ' . sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://support.google.com/analytics/answer/10089681',
                    esc_html__( 'Help me', 'chrssto' )
                ),
        ]
    );

    add_settings_field(
        'chrs_theme_options_gtm',
        __('Google Tag Manager ID', 'chrssto'),
        'chrs_field_pixel_render',
        'simple_tracking',
        'chrssto_options_code',
        [
            'id'      => 'gtmpixel',
            'value'   => $options['gtmpixel'],
            'example' => esc_html__('Ex: GTM-1212121', 'chrssto'). ' - ' . sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://support.google.com/tagmanager/answer/6103696',
                    esc_html__( 'Help me', 'chrssto' )
                ) . '<br /><strong>The '. sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://developer.wordpress.org/reference/functions/wp_body_open/',
                    esc_html__( 'wp_body_open()', 'chrssto' ) ). ' hook MUST be added to your theme</strong>'
        ]
    );

    add_settings_field(
        'chrs_theme_options_fb',
        __('Facebook Pixel ID', 'chrssto'),
        'chrs_field_pixel_render',
        'simple_tracking',
        'chrssto_options_code',
        [
            'id'      => 'fbpixel',
            'value'   => $options['fbpixel'],
            'example' => esc_html__('Ex: 121212121212121', 'chrssto'). ' - ' . sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://www.facebook.com/business/help/742478679120153?id=1205376682832142',
                    esc_html__( 'Help me', 'chrssto' )
                ),
        ]
    );

    add_settings_field(
        'chrs_theme_options_lki',
        __('Linkedin Pixel ID', 'chrssto'),
        'chrs_field_pixel_render',
        'simple_tracking',
        'chrssto_options_code',
        [
            'id'      => 'lkipixel',
            'value'   => $options['lkipixel'],
            'example' => esc_html__('Ex: 2121212', 'chrssto'). ' - ' . sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://www.linkedin.com/help/lms/answer/a415868/access-your-linkedin-insight-tag?lang=en',
                    esc_html__( 'Help me', 'chrssto' )
                ),
        ]
    );

    add_settings_field(
        'chrs_theme_options_pt',
        __('Pinterest Pixel ID', 'chrssto'),
        'chrs_field_pixel_render',
        'simple_tracking',
        'chrssto_options_code',
        [
            'id'      => 'ptpixel',
            'value'   => $options['ptpixel'],
            'example' => esc_html__('Ex: 2323232323232', 'chrssto'). ' - ' . sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://help.pinterest.com/en/business/article/track-conversions-with-pinterest-tag',
                    esc_html__( 'Help me', 'chrssto' )
                ),
        ]
    );

    add_settings_field(
        'chrs_theme_options_pte',
        __('Pinterest Email', 'chrssto'),
        'chrs_field_pixel_render',
        'simple_tracking',
        'chrssto_options_code',
        [
            'id'      => 'ptepixel',
            'value'   => $options['ptepixel'],
            'example' => esc_html__(' Ex: johndoe@domain.com', 'chrssto'). ' - ' . sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    'https://help.pinterest.com/en/business/article/track-conversions-with-pinterest-tag',
                    esc_html__( 'Help me', 'chrssto' )
                ),
        ]
    );

    add_settings_section(
        'chrssto_options_social',
        esc_html__('Social Media Profile Links', 'chrssto'),
        '',
        'simple_tracking'
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
            'simple_tracking',
            'chrssto_options_social',
            [
                'id'      => $social['id'],
                'value'   => $options[$social['id']],
                'example' => $social['example'],
            ]
        );
    }

    register_setting('chrs_options', 'chrs_theme_options', array(
        'sanitize_callback' => function ($input) use ($socials) {
            if (!preg_match('/^(UA|G).*\z/', $input['analytics'])) {
                $input['analytics'] = '';
            }
            $input['analytics'] = sanitize_text_field($input['analytics']);
            $input['fbpixel'] = sanitize_text_field($input['fbpixel']);
            $input['lkipixel'] = sanitize_text_field($input['lkipixel']);
            $input['ptpixel'] = sanitize_text_field($input['ptpixel']);
            $input['ptepixel'] = sanitize_text_field($input['ptepixel']);

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

function chrs_field_pixel_render($args)
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

function chrssto_theme_options_add()
{
    add_menu_page(
        __('Simple Tracking', 'chrssto'),
        __('Simple Tracking', 'chrssto'),
        'edit_theme_options',
        'simple_tracking',
        'chrs_theme_options_do',
        'dashicons-chrssto'
    );
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

function chrssto_add_header()
{
    $themeOptions = get_option('chrs_theme_options');

    $fbID = $themeOptions['fbpixel'];
    $lkiID = $themeOptions['lkipixel'];
    $ptID = $themeOptions['ptpixel'];
    $pteID = $themeOptions['ptepixel'];
    $gtmID = $themeOptions['gtmpixel'];

    if(!empty($gtmID)){
        echo "<!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','" . esc_js($gtmID) . "');</script>
            <!-- End Google Tag Manager -->\n";
    }

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
            <img height='1' width='1' style='display:none' src='" . esc_url("https://www.facebook.com/tr?id=" . $fbID) . "&ev=PageView&noscript=1' />
        </noscript>
        <!-- End Facebook Pixel Code -->\n";
    }

    if(!empty($lkiID)){
        echo "<!-- Linkedin Pixel Code -->
            <script type='text/javascript'>
            _linkedin_partner_id = '" . esc_js($lkiID) . "';
            window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
            window._linkedin_data_partner_ids.push(_linkedin_partner_id);
            </script><script type='text/javascript'>
                (function(){var s = document.getElementsByTagName('script')[0];
                    var b = document.createElement('script');
                    b.type = 'text/javascript';b.async = true;
                    b.src = 'https://snap.licdn.com/li.lms-analytics/insight.min.js';
                    s.parentNode.insertBefore(b, s);})();
            </script>
            <noscript>
                <img height='1' width='1' style='display:none;' alt='' src='" . esc_url("https://px.ads.linkedin.com/collect/?pid=" . $lkiID) . "&fmt=gif' />
            </noscript>
            <!-- End Linkedin Pixel Code -->\n";
    }

    if(!empty($ptID)){
        echo "<!-- Pinterest Tag -->
            <script>
                !function(e){if(!window.pintrk){window.pintrk = function () {
                    window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
                    n=window.pintrk;n.queue=[],n.version='3.0';var
                    t=document.createElement('script');t.async=!0,t.src=e;var
                    r=document.getElementsByTagName('script')[0];
                    r.parentNode.insertBefore(t,r)}}('https://s.pinimg.com/ct/core.js');
                pintrk('load', '" . esc_js($ptID) . "', {em: '" . esc_js($pteID) . "'});
                pintrk('page');
            </script>
            <noscript>
                <img height='1' width='1' style='display:none;' alt='' src='" . esc_url("https://ct.pinterest.com/v3/?event=init&tid=" . $ptID) . "&pd[em]=<hashed_email_address>&noscript=1' />
            </noscript>
            <!-- end Pinterest Tag -->\n";
    }
}


function chrssto_add_body()
{
    $themeOptions = get_option('chrs_theme_options');
    $gtmID = $themeOptions['gtmpixel'];

    if(!empty($gtmID)) {
        echo "\n<!-- Google Tag Manager (noscript) -->
            <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=" . esc_js($gtmID) . "'
            height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->\n";
    }

}

function chrssto_add_footer()
{
    $themeOptions = get_option('chrs_theme_options');
    $gaID = $themeOptions['analytics'];

    if(!empty($gaID)) {
        echo "<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src='".esc_url("https://www.googletagmanager.com/gtag/js?id=" . $gaID ). "'></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', '" . esc_js($gaID) . "');
    </script>
    <!-- End Global site tag (gtag.js) - Google Analytics -->\n";
    }

}

?>
