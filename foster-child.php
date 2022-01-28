<?php

/**
Plugin Name: foster-child
Plugin URI: https://www.oik-plugins.com/oik-plugins/foster-child
Description: Creates a child FSE theme
Version: 0.0.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Text Domain: foster-child
Domain Path: /languages/
License: GPL3
 */

function foster_child_loaded() {
	add_action( "run_foster-child.php", "foster_child_run" );
	add_action( "plugins_loaded", "foster_child_plugins_loaded");
	add_action( 'init', 'foster_child_init' );
}

function foster_child_run() {
	echo "Creating a child theme" . PHP_EOL;
	oik_require( 'classes/class-foster-child.php', 'foster-child');
	$foster_child = new foster_child();
	$foster_child->run();

}

function foster_child_init() {
	add_shortcode( 'foster-child', 'foster_child_shortcode');
	add_filter( "oik_query_autoload_classes" , "foster_child_query_autoload_classes" );
}

function foster_child_shortcode( $atts, $content, $tag ) {
	$html = "Foster child";

	return $html;
}

/**
 * Implements 'plugins_loaded' action for foster-child.
 *
 * Prepares the use of shared libraries if this has not already been done.
 */
function foster_child_plugins_loaded() {
	foster_child_boot_libs();
	oik_require_lib( "bwtrace" );
	oik_require_lib( "bobbfunc" );
	bw_load_plugin_textdomain( "foster-child");
	foster_child_enable_autoload();
}

/**
 * Boot up process for shared libraries
 *
 * ... if not already performed
 */
function foster_child_boot_libs() {
	if ( !function_exists( "oik_require" ) ) {
		$oik_boot_file = __DIR__ . "/libs/oik_boot.php";
		$loaded = include_once( $oik_boot_file );
	}
	oik_lib_fallback( __DIR__ . "/libs" );
}

function foster_child_enable_autoload() {
	$lib_autoload=oik_require_lib( 'oik-autoload' );
	if ( $lib_autoload && ! is_wp_error( $lib_autoload ) ) {
		oik_autoload( true );
	} else {
		BW_::p( "oik-autoload library not loaded" );
		gob();
	}
}

function foster_child_query_autoload_classes( $classes ) {
	$classes[] = array( "class" => "foster_child_shortcode"
	, "plugin" => "foster_class"
	, "path" => "classes"
	, 'file' => 'classes/class-foster-child-shortcode.php'
	);
	/*
	$classes[] = array( "class" => "Git" ,
	                    "plugin" => "oik-batch",
	                    "path" => "includes",
	                    "file" => "includes/class-git.php"

	);
	*/
	return( $classes );
}



foster_child_loaded();