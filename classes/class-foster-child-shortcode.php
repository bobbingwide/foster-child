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
        //if ( !$process_form ) {
            $this->display_form( $atts );
        //}
        return bw_ret();
    }

    function form_id( $set=false) {
        if ( $set ) {
            $this->form_id++;
        }
        return "foster-" . $this->form_id;
    }

    function process_form_preview() {
        BW_::p( "Processing preview");
        $foster_child = new foster_child();
        $foster_child->run_preview();
    }

    function process_form_download() {
        BW_::p( "Processing download");
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


        BW_::bw_select( "template", __( "Parent theme", "oik" ), $current_template,[ '#options' => $themes ] );
        BW_::bw_textfield( 'contentSize', 10, __('Content size', ''), 800, null, null, [ '#type' => 'number'] );
        BW_::bw_textfield( 'wideSize', 10, __('Wide size', ''), 1200, null, null, [ '#type' => 'number'] );
        $units = bw_assoc( bw_as_array( 'px,em,rem,vh,vw,%') );
        BW_::bw_select( 'unit', __( 'Width unit'), 'px', ['#options' => $units ] );
        BW_::bw_textfield( "child", 30, __( "Child theme name", "oik" ), null, "textBox", "!required" );
        etag( "table" );
        e( wp_nonce_field( "_oik_form", "_oik_nonce", false, false ) );
        oik_require_lib( "oik-honeypot" );
        do_action( "oik_add_honeypot" );

        $this->submit_button( 'Download zip', 'download');
        etag( "form" );
        ediv();
    }

}