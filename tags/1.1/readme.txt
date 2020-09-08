=== Simple Theme Options ===
Contributors: chrsinteractive
Plugin URI: http://www.chrsinteractive.com
Tags: theme options, social media, analytics, admin, edit
Requires at least: 3.0.0
Tested up to: 3.9.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With Simple Theme Options plugin your can easily manage your custom theme options such as social media links and Google Analytics code.

== Description ==

The plugin adds an admin interface where you can manage custom theme options.

- Add analytics code
- Add any misc code to header.php and footer.php
- Add social media links (Facebook, Twitter, etc.)

Support

Please submit your support requests through the WordPress forums for [Simple Theme Options](http://wordpress.org/support/plugin/simple-theme-options).

== Installation ==

1. Upload the folder 'simple-theme-options' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Within your dashboard navigation click on 'Theme Options'
4. Place '<?php $themeOptions = get_option( 'chrs_theme_options' ); echo $themeOptions['fburl'] ?>' in your templates
5. change '[fburl]' to the appropriate code for each of the social media links

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.1 =
* Minor CSS + JS improvements

== Upgrade notice ==