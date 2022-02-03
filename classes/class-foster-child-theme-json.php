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
	    $this->theme_json = json_decode( $contents, true );
    }

    function get_adjusted() {
	    $adjusted = json_encode( $this->theme_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
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

    /**
     * Updates the field with the given value.
     *
     * The contentSize, wideSize and blockGap fields may be numeric, with a simple unit string.
     * In this case we expect to get a field-unit input field as well.
     * If not, then we just take what we've been given.
     *
     * @param $field
     * @param $field_value
     */
    function update_property( $field, $field_value ) {
	    switch ( $field ) {
            case 'contentSize':
                if ( is_numeric(  $field_value )) {
                    $unit = $this->get_field_value('contentSize-unit');
                    $unit = $this->validate_unit($unit);
                    $field_value .= $unit;
                }
                $this->property_set( 'settings.layout.contentSize', $field_value  );
                break;
            case 'wideSize':
                if ( is_numeric(  $field_value )) {
                    $unit = $this->get_field_value('wideSize-unit');
                    $unit = $this->validate_unit($unit);
                    $field_value .= $unit;
                }
                $this->property_set( 'settings.layout.wideSize', $field_value  );
                break;

            case 'blockGap':
                //print_r( $this->theme_json );
                //print_r( $this->theme_json->styles);
                //print_r( $this->theme_json->styles->spacing);
                // print_r( $this->theme_json->styles->spacing->blockGap );
                if ( is_numeric(  $field_value )) {
                    $unit = $this->get_field_value('blockGap-unit');
                    $unit = $this->validate_unit($unit);
                    $field_value .= $unit;
                }
                $this->property_set( 'styles.spacing.blockGap', $field_value  );
                break;

            default:
                p( "Unrecognized field: $field");

        }
    }

    /**
     * Sets a nested array property to the given value.
     *
     * Creates empty parent arrays where necessary.
     *
     * @param $property property to set.  eg styles.spacing.blockGap
     * @param $value value to set the property to eg 0px
     */
    function property_set( $property, $value ) {
        $properties = $this->create_nested_array( $property, $value );
        $this->theme_json = array_replace_recursive( $this->theme_json, $properties );
    }

    /**
     * Creates a multi-dimensional array.
     *
     * Builds the array up inside out.
     *
     * @param $properties array structure defined as dot separated node. eg `styles.spacing.blockGap`
     * @param $value value for final node. eg `0px`
     * @return array
     */
    function create_nested_array( $properties, $value ) {
        $explode = explode( '.', $properties );
        $folder = $value;
        for ( $counter = count($explode) - 1; $counter >= 0; $counter--) {
            $folder = [$explode[$counter] => $folder];
        }
        return $folder;
    }

}