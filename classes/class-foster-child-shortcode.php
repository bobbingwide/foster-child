<?php

/**
 * @copyright (C) Copyright Bobbing Wide 2022
 * @package foster-child
 */

class foster_child_shortcode
{

    private $form_id;

    function __construct() {
        $form_id = 0;
    }

    function run( $atts, $content, $tag ) {
        $form_id = $this->form_id( true );
        //echo $form_id;
        //print_r( $_REQUEST);
        $process_form_preview = bw_array_get( $_REQUEST, $form_id . 'preview', null );
        $process_form_download = bw_array_get( $_REQUEST, $form_id . 'download', null);
        if ( $process_form_preview || $process_form_download ) {
            oik_require_lib( "bobbforms" );
            oik_require_lib( "oik-honeypot" );
            do_action( "oik_check_honeypot", "Human check failed." );
            $process_form = bw_verify_nonce( "_oik_form", "_oik_nonce" );
            if ( $process_form ) {
                if ( $process_form_preview ) {
                    $process_form = $this->process_form_preview();
                } else {
                    $process_form = $this->process_form_download();
                }
            } else {
                p( "Invalid form submission");
            }
        }

        $this->display_form( $atts );

        return bw_ret();
    }

    function form_id( $set=false) {
        if ( $set ) {
            $this->form_id++;
        }
        return "foster-" . $this->form_id;
    }

    function process_form_preview() {
        //BW_::p( "Processing preview");
        $foster_child = new foster_child();
        $foster_child->run_preview();
    }

    function process_form_download() {
        //BW_::p( "Processing download");
        $foster_child = new foster_child();
        $foster_child->run_download();
    }

    function submit_button( $label, $name ) {
        e( isubmit( $this->form_id() . $name, $label, null ) );
    }

    function display_form( $atts ) {
        //BW_::p( "Displaying form");

        $oik_patterns_from_htm = new OIK_Patterns_From_Htm();
        $oik_patterns_from_htm->list_themes();
        $themes = $oik_patterns_from_htm->get_themes();

        $current_theme = wp_get_theme();
        $current_template = $current_theme->get_template();
        $current_stylesheet = $current_theme->get_stylesheet();

        oik_require( "bobbforms.inc" );
        $class = bw_array_get( $atts, "class", "foster-child" );
        sdiv( $class );
        bw_form();
        stag( "table" );
        BW_::bw_select( 'preview_theme', "Theme style", $current_stylesheet, [ '#options' => $themes ] );
        // Using preview theme for oik-patterns to use.
        //BW_::bw_textfield( 'preview_theme', 30, "Preview theme", null, 'textBox');
        stag( "tr");
        stag( 'td');
        $this->submit_button( 'Preview theme', 'preview'  );
        etag( 'td');
        etag( "tr");

        $contentSize = $this->getcontentSize();
        $wideSize = $this->getwideSize();
        $blockGap = $this->getblockGap();

        BW_::bw_select( "template", __( "Parent theme", "oik" ), $current_template,[ '#options' => $themes ] );
        //BW_::bw_textfield( 'contentSize', 10, __('Content size', ''), $contentSize,null,  null, [ ] );
        $this->sizeunitfield( 'contentSize', 4, __('Content size'), $contentSize );
        $this->sizeunitfield( 'wideSize', 4, __('Wide size', ''), $wideSize );
        $this->sizeunitfield( 'blockGap', 4, __('Block gap', ''), $blockGap );
        //BW_::bw_textfield( 'blockGap', 4, __('Block gap', ''), $blockGap, null, null, ['#type' => 'number'] );
        //$units = bw_assoc( bw_as_array( 'px,em,rem,vh,vw,%') );
        //BW_::bw_select( 'unit', __( 'Width unit'), 'px', ['#options' => $units ] );
        BW_::bw_textfield( "child", 30, __( "Child theme name", "oik" ), null, "textBox", "!required" );
        etag( "table" );
        e( wp_nonce_field( "_oik_form", "_oik_nonce", false, false ) );
        oik_require_lib( "oik-honeypot" );
        do_action( "oik_add_honeypot" );

        $this->submit_button( 'Download zip', 'download');
        etag( "form" );
        ediv();
    }

    function getcontentSize() {
        $contentSize = 	wp_get_global_settings( array( 'layout', 'contentSize' ) );
        if ( !is_scalar( $contentSize )) {
            $contentSize = '';
        }
        return $contentSize;
    }

    function getwideSize() {
        $wideSize = 	wp_get_global_settings( array( 'layout', 'wideSize' ) );
        if ( !is_scalar( $wideSize )) {
            $wideSize = '';
        }
        return $wideSize;
    }

    function getblockGap() {
        $blockGap = wp_get_global_styles( array( 'spacing', 'blockGap' ) );
        if ( !is_scalar( $blockGap )) {
            $blockGap = '';
        }
        return $blockGap;
    }

    /**
     * Displays a numeric field followed by the unit field.
     *
     * When the field's not numeric then use a single input field.
     *
     * @param $name
     * @param $width
     * @param $title
     * @param $value
     */
    function sizeunitfield( $name, $len, $text, $value ) {

        // Note: rem before em otherwise rem gets handled incorrectly.
        $units = bw_assoc( bw_as_array( 'px,rem,em,vh,vw,%') );
        if ( $value === null ) {
            $size = bw_array_get( $_REQUEST, $name, null );
            $unit = bw_array_get( $_REQUEST, "$name-unit", null );

        } else {
            $size = $this->getsize( $value, $units );
            $unit = $this->getunit( $value, $units );
        }
        $lab = label( $name, $text );
        if ( is_numeric( $size )) {
            $itext = itext($name, $len, $size, null, null, ['#type' => 'number']);
            $iselect = iselect("$name-unit", $unit, ['#options' => $units]);
            bw_tablerow(array($lab, $itext, $iselect));
        } else {
            $itext = itext( $name, strlen( $value ), $value);
            bw_tablerow(array($lab, $itext ));
        }

    }

    /**
     * Gets the numeric part of the size field.
     */
    function getsize( $value, $units ) {
        //print_r( $value );
        foreach ( $units as $unit ) {
            if ( false !== strpos( $value, $unit )) {
                $value = str_replace( $unit, '', $value );
                break;
            }
        }
        //echo 'size' . $value;
        return $value;
    }

    /**
     * Gets the unit part of the size field.
     *
     * @param $value
     * @param $units
     * @return mixed
     */
    function getunit( $value, $units ) {
        foreach ( $units as $unit ) {
            if ( false !== strpos( $value, $unit )) {
                $value = $unit;
                break;
            }
        }
        //echo 'unit' . $value;
        return $value;
    }



}