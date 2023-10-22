=== foster-child ===
Contributors: bobbingwide, vsgloik
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: child-theme, generator, full-site-editing
Requires at least: 5.9
Tested up to: 6.4-RC1
Stable tag: 0.2.0

== Description ==
The Foster child plugin creates child themes for Full Site Editing themes. 

The Foster child plugin provides a shortcode to enable you to create a child theme from any Full Site Editing theme installed in your site.  
Choose the parent theme for the templates and another theme to provide the styling ( from theme.json ). 
Also apply some configuration changes such as content width and block gap.  

Create thousands of child themes without any programming.

== Installation ==
1. Upload the contents of the foster-child plugin to the `/wp-content/plugins/foster-child' directory
1. Activate the foster-child plugin through the 'Plugins' menu in WordPress
1. Create a post or page containing the [foster-child] shortcode.
1. Visit the post.
1. Complete the fields in the form and choose Download zip.
1. Save the generated .zip file.
1. Install the generated child theme.

== Screenshots ==
1. Foster-child shortcode 

== Upgrade Notice ==
= 0.2.0 = 
Update for support for PHP 8.1 and PHP 8.2 

= 0.1.0 = 
Version for alpha testing on blocks.wp-a2z.org


== Changelog ==
= 0.2.0 = 
* Changed: Support PHP 8.1 and PHP 8.2,#7
* Changed: WordPress 5.9 or higher needed
* Changed: Treat theme.json as associate array to adjust properties #4
* Changed: Cater for varying units or complex input. #4
* Tested: With WordPress 6.4-RC1 and WordPress Multisite
* Tested: With Gutenberg 16.8.1
* Tested: With PHP 8.0, PHP 8.1 and PHP 8.2

= 0.1.0 = 
* Added: New plugin provides the [foster-child] shortcode to generate new child themes.
* Tested: With WordPress 5.9
* Tested: With Gutenberg 12.4.1
* Tested: With PHP 8.0