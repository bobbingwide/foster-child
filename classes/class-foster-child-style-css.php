<?php

class foster_child_style_css {

	private $style;

	function __construct( $child, $parent_theme) {

		$style = [];
		$style[] = '/*';
		$style[] = ' Theme Name: ' . $child;
		$style[] = ' Description: Child theme of ' . $parent_theme->display( 'Name');
		$style[] = ' Version: 0.0.0';
		$style[] = ' Tags: full-site-editing';
		$style[] = ' Template: ' . $parent_theme->get_template(); // For when the parent theme is itself a child theme
		$style[] = ' Text domain: ' . $child;
		$style[] = '*/';

		//print_r( $style );
		$this->style = $style;

	}

	function get_contents() {
		$contents = implode( PHP_EOL, $this->style );
		return $contents;
	}


	/*
Theme Name:
Theme URI: https://www.oik-plugins.com/oik-themes/fizzie
Description: Fizzie theme - a Full Site Editing theme using Gutenberg blocks
Author: Bobbing Wide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Version: 1.0.0
Tags: blocks, Gutenberg, FSE, oik, Full Site Editing
Requires at least: 5.5.1
Requires PHP: 7.3
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text domain: fizzie
*/

}