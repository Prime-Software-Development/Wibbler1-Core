<?php
namespace Trunk\Tinc;
require_once(__dir__ . '/BaseController.php');
$_ns = '\\Trunk\\Tinc';

class Jscss extends \Trunk\Tinc\BaseController {

	var $bl_bypass_security = true;

	function js( ) {

		$this->_process( 'js' );

	}

	function css( ) {

		$this->_process( 'css' );

	}

	function common() {
		session_write_close();

		array_shift( $this->url_parts );
		// Get type
		$type = $root_directory = array_shift( $this->url_parts );

		$this->_process( $type, 'web_common' );
	}

	private function _process( $type = 'js', $root_dir = null ) {

		// get full path to the file
		$filename = $this->get_path( $type, $root_dir );

		if ( file_exists($filename) ) {
			if ( $type == 'css' ) {
				header( "Content-type: text/css" );
			}
			else {
				header( "Content-type: text/javascript" );
			}
			readfile( $filename );
		}
		else {
			http_response_code( 404 );
		}

	}

	function get_path( $type, $root = null ) {

		$root_directory = $root;
		if( ! $root ) {
			array_shift( $this->url_parts );
			// Find if we're to use web1, 2 or 3
			$root_directory = array_shift( $this->url_parts );
		}

		return $filename = BASEPATH . '/../' . $root_directory . '/' . $type . '/' . implode("/", $this->url_parts );
	}
}