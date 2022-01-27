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
}

function foster_child_run() {
	echo "Creating a child theme" . PHP_EOL;
	oik_require( 'classes/class-foster-child.php', 'foster-child');
	$foster_child = new foster_child();
	$foster_child->run();

}

foster_child_loaded();