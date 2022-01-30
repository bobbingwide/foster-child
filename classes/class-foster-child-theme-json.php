<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2022
 * @package foster-child
 */

class foster_child_theme_json {
	private $child;
	private $theme_json_theme;

	function __construct( $child, $theme_json_theme) {
		$this->child = $child;
		$this->theme_json_theme = $theme_json_theme;
	}

	function get_contents() {
		$theme_json_file = $this->get_theme_json_theme_folder( 'theme.json');
		p( "Theme json file: " . $theme_json_file );
		$contents = file_get_contents( $theme_json_file );
		//$contents = implode( PHP_EOL, $this->style );
		return $contents;
	}

	function get_theme_json_theme_folder( $filename ) {
		$full_filename = $this->theme_json_theme->get_stylesheet_directory();
		$full_filename .= '/';
		//$full_filename = str_replace( '/' . $this->theme_json_theme->get_stylesheet() .'/', '/' . $this->child . '/', $full_filename );
		$full_filename .= $filename;
		return $full_filename;

	}

}