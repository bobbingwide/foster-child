<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2022
 * @package foster-child
 */

class foster_child_theme_json {
	private $child;
	private $theme_json_theme;

	private $theme_json = null;

	function __construct( $child, $theme_json_theme) {
		$this->child = $child;
		$this->theme_json_theme = $theme_json_theme;
	}

	function load( ) {
	    $contents = $this->get_contents();
	    $this->theme_json = json_decode( $contents );
    }

    function get_adjusted() {
	    $adjusted = json_encode( $this->theme_json, JSON_PRETTY_PRINT );
	    return $adjusted;
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

	function apply( $field ) {
	    $field_value = $this->get_field_value( $field );
	    if ( !empty( $field_value ) ) {
	        $this->update_property( $field, $field_value );
        }
    }

    function get_field_value( $field ) {
        $field_value = bw_array_get( $_REQUEST, $field, null);
        // sanitize field value?
        return $field_value;
    }

    function validate_unit( $unit ) {
        $units = bw_assoc( bw_as_array( 'px,em,rem,vh,vw,%') );
        $unit = bw_array_get( $units, $unit, 'px' );
        return $unit;
    }

    function update_property( $field, $field_value ) {
	    switch ( $field ) {
            case 'contentSize':
                $unit = $this->get_field_value( 'unit');
                $unit = $this->validate_unit( $unit);
                $this->theme_json->settings->layout->contentSize = $field_value . $unit;
                break;
            case 'wideSize':
                $unit = $this->get_field_value( 'unit');
                $unit = $this->validate_unit( $unit);
                $this->theme_json->settings->layout->wideSize = $field_value . $unit;
                break;

            case 'blockGap':
                $unit = $this->get_field_value( 'unit');
                $unit = $this->validate_unit( $unit);
                $this->theme_json->styles->spacing->blockGap = $field_value . $unit;

            default:
                p( "Unrecognized field: $field");

        }
    }

}