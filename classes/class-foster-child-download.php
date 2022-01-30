<?php

/**
 * @copyright (C) Copyright Bobbing Wide 2022
 * @package foster-child
 */

class foster_child_download
{

    private $child;
    private $child_theme;

    private $filename;
    private $style_css;
    private $theme_json;

    function __construct( $child, $child_theme, $style_css, $theme_json ) {
        $this->child = $child;
        $this->child_theme = $child_theme;
        $this->style_css = $style_css;
        $this->theme_json = $theme_json;
        $this->filename = tempnam( get_temp_dir(), $child );
        p( "Downloading child theme: $child via " . $this->filename );

    }

    /**
     * Runs the Download action.
     */
    function run_download() {
        $this->create_theme_zip();
        $this->download_theme_zip();
    }

    /**
     * Creates a child theme zip file.
     *
     * Creates an export of the current templates and
     * template parts from the site editor at the
     * specified path in a ZIP file.
     *
     * Logic copied and cobbled from https://github.com/Automattic/create-blockbase-theme
     */
    function create_theme_zip( ) {

        if ( ! class_exists( 'ZipArchive' ) ) {
            return new WP_Error( 'Zip Export not supported.' );
        }

        $zip = new ZipArchive();
        $zip->open( $this->filename, ZipArchive::OVERWRITE );
        $zip->addEmptyDir( $this->child );

        // Add theme.json.
        // Add screenshot.png.

        $zip->addFromString( $this->child . '/style.css', $this->style_css );

        //$style_css =
        //$zip->addFile(
        //    __DIR__ . '/screenshot.png',
        //    $theme['slug'] . '/screenshot.png'
       // );
        $zip->addFromString($this->child . '/theme.json', $this->theme_json );

        // @TODO Add readme.txt, screenshot.png
        // Save changes to the zip file.
        $zip->close();
    }

    /**
     * Output a ZIP file with an export of the current templates
     * and template parts from the site editor, and close the connection.
     */
    function gutenberg_edit_site_export_theme( $theme ) {
        // Sanitize inputs.
        $theme['name'] = sanitize_text_field($theme['name']);
        $theme['description'] = sanitize_text_field($theme['description']);
        $theme['uri'] = sanitize_text_field($theme['uri']);
        $theme['author'] = sanitize_text_field($theme['author']);
        $theme['author_uri'] = sanitize_text_field($theme['author_uri']);

        $theme['slug'] = sanitize_title($theme['name']);
        // Create ZIP file in the temporary directory.
        $filename = tempnam(get_temp_dir(), $theme['slug']);
        gutenberg_edit_site_export_theme_create_zip($filename, $theme);
    }

    /**
     * Downloads the temporary zip file.
     *
     * bw_ret() discards any other HTML output from the shortcode.
     */
     function download_theme_zip() {
        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename=' . $this->child . '.zip' );
        header( 'Content-Length: ' . filesize( $this->filename ) );
        flush();
        readfile( $this->filename );
        $discard = bw_ret();
        die();
    }

}