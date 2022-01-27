<?php

/**
 * @copyright (C) Copyright Bobbing Wide 2022
 * @package foster-child
 */

class foster_child {

	private $child; // name of the child theme eg baby defautls to t followed by the current time
	private $child_theme; // WP_theme object for the child theme - there shouldn't be one
	private $template; // name of the parent theme eg twentytwentytwo
	private $template_theme; // WP_Theme object for the parent theme

	private $theme_json; // name of the theme.json file
	private $theme_json_theme; // WP_Theme object for the theme providing theme.json

	function __construct() {
		$this->child = null;
	}

	function run() {

		$template = oik_batch_query_value_from_argv( 1, 'twentytwentytwo' );
		$theme_json = oik_batch_query_value_from_argv( 2, $template );
		if ( '.' === $theme_json || empty( $theme_json)  ) {
			$theme_json = $template;
		}
		$child = oik_batch_query_value_from_argv( 3, 't' . time() );

		$delete_child = oik_batch_query_value_from_argv( 'delete', 'n' );

		echo "Creating child theme $child for parent theme $template with theme.json from $theme_json" . PHP_EOL;
		if ( !$this->validate_child_theme( $child ) ) {
			if ( 'y' !== $delete_child ) {
				echo "Error: Child theme already exists";
				return;
			}
			echo "Overwriting child: " . $child;
		}

		if ( !$this->validate_parent_theme( $template ) ) {
			echo "Error: Parent theme no good";
			return;
		}

		if ( !$this->validate_theme_json_theme( $theme_json ) ) {
			echo "Error: theme json theme no good";
			return;
		}
		$this->build_child_theme();
	}

	/**
	 * Validates the child theme.
	 *
	 * If we pass a null value to wp_get_theme() we get the current theme.
	 * If the theme doesn't exist the WP_Theme returned contains a private WP_Error object
	 *
	 * @param $child
	 *
	 * @return bool
	 */
	function validate_child_theme( $child) {
		$this->child=$child;
		$this->child_theme = wp_get_theme( $child );
		//print_r( $this->child_theme );
		if ( $this->child_theme->exists() ) {
			return false;
		}

		return true;
	}

	function validate_parent_theme( $template ) {
		$this->template = $template;
		$this->template_theme = wp_get_theme( $template );
		if ( !$this->template_theme->exists()) {
			return false;
		}
		if ( $this->template_theme->get_template() !== $this->template_theme->get_stylesheet() ) {
			echo "Parent theme is already a child theme";
			return false;
		}
		return true;
	}

	function validate_theme_json_theme( $theme_json ) {
		echo "Validating json theme: " . $theme_json . PHP_EOL;
		$this->theme_json = $theme_json;
		$this->theme_json_theme = wp_get_theme( $theme_json );
		print_r( $this->theme_json_theme );
		if ( !$this->theme_json_theme->exists()) {
			return false;
		}
		if ( $this->template_theme->get_template() !== $this->template_theme->get_stylesheet() ) {
			echo "Theme.json theme is already a child theme";
			return false;
		}
		return true;
	}

	function build_child_theme() {
		$this->build_style_css();
		$this->build_theme_json();
		$this->build_index_html();
		$this->build_functions_php();
		$this->build_readme_txt();
	}

	function build_style_css() {
		oik_require( 'classes/class-foster-child-style-css.php', 'foster-child');
		$style = new foster_child_style_css( $this->child, $this->template_theme );
		$style_contents = $style->get_contents();
		$this->write_theme_file( 'style.css', $style_contents);


	}
	function build_theme_json() {


	}

	function build_index_html() {

	}

	function build_functions_php() {

	}

	function build_readme_txt() {

}



	function write_theme_file( $filename, $contents ) {
		$full_filename = $this->get_fullname_mkdir( $filename );
		echo "writing theme file: $filename" . PHP_EOL;
		echo "full file name: " . $full_filename . PHP_EOL;
		echo $contents;
		echo PHP_EOL;
		file_put_contents( $full_filename, $contents);

	}

	function get_target_theme_folder( $filename ) {
		$full_filename = $this->template_theme->get_stylesheet_directory();
		$full_filename .= '/';
		$full_filename = str_replace( '/' . $this->template .'/', '/' . $this->child . '/', $full_filename );
		$full_filename .= $filename;
		return $full_filename;

	}

	function get_fullname_mkdir( $filename ) {
		$full_filename = $this->get_target_theme_folder( $filename );
		$parts = explode( '/', $full_filename);
		array_pop( $parts );
		$path = implode( '/', $parts);
		echo "Creating folder: $path" . PHP_EOL;
		wp_mkdir_p( $path );
		return $full_filename;
	}

}