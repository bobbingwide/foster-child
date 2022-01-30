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

    /**
     * Builds a child theme for a batch command.
     *
     * This was the original prototype code.
     */
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
     *
     * It would appear that we can't control which directory theme.json is being loaded from.
     * In order to support preview we'd need to generate the child theme
     * then run the preview_theme logic from oik-patterns.
     * Leaving the form processing to the shortcode would appear to be too late.
     *
     * Suggests we need a generate button, then a preview then a download.
     * With 100 themes there could be 10,000 child theme combinations
     * Probably not ideal? WordPress.org's got few than that on its books.
     */
	function run_preview() {
	    $this->run_form();
    }

    function run_download() {
	    $valid = $this->run_form();
	    if ( !$valid ) {
	        return;
        }
	    $style_css = $this->get_style_css();
	    $theme_json_contents = $this->get_theme_json_contents();
	    $foster_child_download = new foster_child_download( $this->child, $this->child_theme, $style_css, $theme_json_contents );
	    $foster_child_download->run_download();
    }

    /**
     * Validate the form.
     *
     * @return bool
     */
    function run_form() {
	    p( "Getting fields from form ");
	    $template = $this->get_template();
	    $theme_json =$this->get_theme_json();
	    $child = $this->get_child();
	    // validate_file() doesn't really check the file name. It accepted .fred
	    // $valid_name = validate_file( $child );
	    //echo "Validate file result: " . $valid_name . PHP_EOL;
	    $valid_child = $this->validate_child_theme( $child );
	    $valid_parent = $this->validate_parent_theme( $template );
	    $valid_theme_json = $this->validate_theme_json_theme( $theme_json );
	    $valid = $valid_child && $valid_parent && $valid_theme_json;
	    return $valid;
    }

    function get_field( $field ) {
	    return bw_array_get( $_REQUEST, $field, null );
    }

    function get_template() {
	    $template = $this->get_field( 'template');
	    return $template;
    }

    /**
     * @TODO Change field name for theme_json to preview_theme.
     *
     * @return mixed|null
     */
    function get_theme_json() {
        $theme_json = $this->get_field( 'theme_json');
        return $theme_json;
    }

    function get_child() {
        $child = $this->get_field( 'child');
        return $child;
    }

    function get_contentSize() {
        $contentSize = $this->get_field( 'contentSize');
        return $contentSize;
    }

    function wideSize() {
        $wideSize = $this->get_field( 'wideSize');
        return $wideSize;
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

		$sanitized = sanitize_title_with_dashes( $child );
		if ( $sanitized !== $child ) {
		    p("Correct name from: $child to $sanitized" );
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
		p( "Validating json theme: " . $theme_json );
		$this->theme_json = $theme_json;
		$this->theme_json_theme = wp_get_theme( $theme_json );
		//print_r( $this->theme_json_theme );
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

	function get_style_css() {
        oik_require('classes/class-foster-child-style-css.php', 'foster-child');
        $style = new foster_child_style_css($this->child, $this->template_theme);
        $style_contents = $style->get_contents();
        return $style_contents;
    }

	function build_style_css() {
	    $style_contents = $this->get_style_css();
		$this->write_theme_file( 'style.css', $style_contents);
	}

	function get_theme_json_contents() {
	    oik_require( 'classes/class-foster-child-theme-json.php', 'foster-child');
        $theme_json = new foster_child_theme_json( $this->child, $this->theme_json_theme );
        $theme_json_contents = $theme_json->get_contents();
        return $theme_json_contents;

    }

	function build_theme_json() {
		if( $this->template === $this->theme_json ) {
			return;
		}
		$theme_json_contents = $this->get_theme_json_contents();
		$this->write_theme_file( 'theme.json', $theme_json_contents);
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
		//echo $contents;
		//echo PHP_EOL;
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