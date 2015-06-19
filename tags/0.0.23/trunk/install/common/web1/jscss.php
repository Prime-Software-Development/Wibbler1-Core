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

	private function _process( $type = 'js' ) {

		array_shift( $this->url_parts );
		// Find if we're to use web1, 2 or 3
		$root_directory = array_shift( $this->url_parts );

		$filename = BASEPATH . '/../' . $root_directory . '/' . $type . '/' . implode("/", $this->url_parts );
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
}