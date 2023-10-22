<?php

/**
 * @package foster-child
 * @copyright (C) Copyright Bobbing Wide 2023
 *
 * Unit tests to load all the PHP files for PHP 8.2
 */
class Tests_load_php extends BW_UnitTestCase
{

	/**
	 * set up logic
	 *
	 * - ensure any database updates are rolled back
	 * - we need oik-googlemap to load the functions we're testing
	 */
	function setUp(): void 	{
		parent::setUp();
	}

	function test_load_classes_php() {
		oik_require( 'classes/class-foster-child.php', 'foster-child');
		oik_require( 'classes/class-foster-child-download.php', 'foster-child');
		oik_require( 'classes/class-foster-child-readme-txt.php', 'foster-child');
		oik_require( 'classes/class-foster-child-shortcode.php', 'foster-child');
		oik_require( 'classes/class-foster-child-style-css.php', 'foster-child');
		oik_require( 'classes/class-foster-child-theme-json.php', 'foster-child');
		$this->assertTrue( true );
	}

	function test_load_libs() {

		$files = glob( 'libs/*.php');
		//print_r( $files );
		foreach ( $files as $file ) {
			oik_require( $file, 'foster-child');
		}
		$this->assertTrue( true );

	}

	function test_load_plugin_php() {
		oik_require( 'foster-child.php', 'foster-child');
		$this->assertTrue( true );
	}
}

