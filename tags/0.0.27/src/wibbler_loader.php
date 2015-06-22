<?php
namespace Trunk\Wibbler;
if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

/**
 * Try to find and create the user's controller
 */
class WibblerLoader {

	/**
	 * The path to the class file
	 * @var string
	 */
	var $class_file = null;
	/**
	 * The name of the class
	 * @var string
	 */
	var $class_name = null;
	/*
	 * Path to the class
	 * @var array
	 */
	var $class_path = [ ];
	/**
	 * The full class name (including namespace)
	 * @var string
	 */
	var $full_class_name = null;
	/**
	 * The method to call within the class
	 * @var string
	 */
	var $class_method = null;
	/**
	 * Any fatal error found when trying to load the class / method or false if all is ok
	 * @var mixed
	 */
	var $error = false;
	/**
	 * The parts of the url which need passing to the method
	 * @var array
	 */
	var $url_parts = array();
	/**
	 * The controller which matches the given path
	 * @var WibblerController
	 */
	var $controller = null;

	/**
	 * The path to the controller
	 */
	var $controller_path;

	function __construct() {

		$path_parts = $this->init();

		$initial_path = CONTROLLERPATH;

		// If the controller file can't be found - return
		if ( !$this->check_path( $initial_path, $path_parts ) )
			return;

		// If the controller path isn't the root
		if ( $this->controller_path != '/' ) {
			// Work out the real path
			$this->controller_path = substr( $this->controller_path, strlen( $initial_path ) );
		}

		// Try to load the controller
		$this->controller = $this->check_class();

		// If the loading has failed - return
		if ( $this->controller === false ) {
			$this->error = "No controller found";
			return;
		}

		// Set the path to the controller within the controller
		$this->controller->_set_controller_details( $this->controller_path, $this->class_path );

		// Check the method exists within the controller
		if ( $this->check_method() === false )
			return;
	}

	protected function init() {

		// If PHP_SAPI == 'cli' we have been called from the command line - process argv
		if ( PHP_SAPI == 'cli' ) {
			global $argv;
			$arguments = $argv;
			array_shift( $arguments );
			return $arguments;
		}

		if ( isset( $_SERVER[ 'REDIRECT_QUERY_STRING' ] ) ) {
			$path = substr( $_SERVER[ 'REDIRECT_QUERY_STRING' ], 1 );
		} else {
			$path = substr( $_SERVER[ 'REQUEST_URI' ], 1 );
		}

		// If there is a question mark
		if ( strpos( $path, "?") !== false ) {
			$path = substr( $path, 0, strpos( $path, "?") );
		}

		$parts = explode( '/', $path );
		// Remove the empty element from the array
		if ( $parts[ count( $parts ) - 1 ] == '' )
			array_pop( $parts );
		if ( isset( $parts[ 0 ] ) && $parts[ 0 ] == 'index.php' ) {
			array_shift( $parts );
		}
		return $parts;
	}

	/**
	 * Checks the path to the controller - makes sure the contoller exists
	 * @param type $path
	 * @param type $parts
	 */
	private function check_path( $path, $parts ) {

		// Default index file name (usually welcome)
		global $index_class;
		// Initial path to the controller
		$this->controller_path = "/";

		// Whether a match has been made
		$match = false;
		// Whether a match cannot be made
		$match_fail = false;

		$this->class_path = [ ];
		$new_path = $path;

		// Iterate over the parts of the url
		foreach ( $parts as $index => $part ) {

			$test_path = $new_path . $part;

			// Check if the controller file exists
			if ( file_exists( $test_path . '.php' ) ) {

				// Controller file has been found :-)
				// Note the class name is the last part checked
				$this->class_name = $part;
				// Note the url matches
				$match = true;
				// Split the remaining parts of the url
				$this->url_parts = array_slice( $parts, $index + 1 );
				$new_path = $test_path . '.php';
				break;
			} // Else if the path is a directory
			elseif ( is_dir( $test_path ) ) {
				// Continue to check the next element
				$new_path = $test_path . '/';
				$this->controller_path = $new_path;
			} // Matching has failed - neither a file or directory match the path :-(
			else {
				$match_fail = true;
				break;
			}

			// Keep track of the url parts we've used to locate the controller
			$this->class_path[ ] = $part;
		}

		// If there is a positive failure
		if ( $match_fail ) {
			// Warn the user and exit
			echo "No controller found :-(<br/>";
			return false;
		} // No failure, but no success so far
		elseif ( !$match ) {
			// Add the index class (usually welcome) to the url and check again
			$new_path = $new_path . '/' . $index_class . '.php';
			if ( file_exists( $new_path ) ) {
				$this->class_name = $index_class;
				$match = true;
			}
		}

		// We've matched :-) - note the path
		if ( $match ) {
			$this->class_file = $new_path;
		} // No match - warn the user
		else {
			$this->error = "No controller found :-(<br/>";
		}

		return $match;
	}

	/**
	 * Checks the controller class exists
	 * @return boolean
	 */
	private function check_class() {

		include $this->class_file;

		if ( !isset( $_ns ) )
			$_ns = '\\Wibbler\\User\\Modules';
		$this->full_class_name = $_ns . "\\" . $this->class_name;

		if ( class_exists( $this->full_class_name ) ) {
			$controller = new $this->full_class_name();
			$controller->url_parts = $this->url_parts;
			return $controller;
		} else {
			$this->error = 'Class not found';
			return false;
		}
	}

	/**
	 * Checks the method to be called exists
	 * @global type $index_method
	 * @return boolean
	 */
	private function check_method() {

		global $index_method;

		if ( !empty( $this->url_parts[ 0 ] ) ) {
			$this->class_method = $this->url_parts[ 0 ];
			$this->url_parts = array_slice( $this->url_parts, 1 );
		} else
			$this->class_method = $index_method;
		$method = $this->class_method;
//echo "<strong>" . $method . "</strong><br/>";

		// If the method name begins with an underscore
		if ( substr( $method, 0, 1 ) == "_" ) {
			// Note the method is unavailable - underscores are reserved for internal use only
			$this->error = 'Method unavailable';
			// Return
			return;
		}

		if ( method_exists( $this->controller, $method ) ) {
//			$this->controller->$method();
			// Create some reflection to confirm parameter counts are ok

			// Start reflecting the class
			$class = new \ReflectionClass( $this->full_class_name );
			// Get the method details
			$method = $class->getMethod( $method );
			// Find the number of required parameters
			$required_params = $method->getNumberOfRequiredParameters();

			// If the number of required parameters is greater than the number given
			if ( $required_params > count( $this->url_parts ) ) {
				// Set an error
				$this->error = 'Not enough parameters';
				return;
			}

			return true;
		} else {
			$this->error = 'Method not found';
		}
	}
}