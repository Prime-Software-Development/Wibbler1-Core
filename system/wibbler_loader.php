<?php
namespace Wibbler;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
	 * @var mixed
	 */
	var $controller = null;

	/**
	 * The path to the controller
	 */
	var $controller_path;

	function __construct() {

		$path_parts = $this->init();

		$initial_path = __dir__ . '/../application/controllers/';

		// If the controller file can't be found - return
		if(!$this->check_path($initial_path, $path_parts))
			return;

		// If the controller path isn't the root
		if ($this->controller_path != '/') {
			// Work out the real path
			$this->controller_path = substr($this->controller_path, strlen($initial_path));
		}

		// Try to load the controller
		$this->controller = $this->check_class();
		// If the loading has failed - return
		if ($this->controller === false)
			return;

		// Set the path to the controller within the controller
		$this->controller->controller_path = $this->controller_path;

		// Check the method exists within the controller
		if ($this->check_method() === false)
			return;
	}

	protected function init() {
		if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
			$path = substr($_SERVER['REDIRECT_QUERY_STRING'], 1);
		}
		else {
			$path = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']) + 1);
		}
		
		$parts = explode('/', $path);
		// Remove the empty element from the array
		if ($parts[count($parts) - 1] == '')
			array_pop($parts);
		return $parts;
	}

	/**
	 * Checks the path to the controller - makes sure the contoller exists
	 * @param type $path
	 * @param type $parts 
	 */
	private function check_path($path, $parts) {

		global $index_class;
		$this->controller_path = "/";
		
		$match = false;
		$match_fail = false;

		$new_path = $path;
		foreach ($parts as $index => $part) {
//echo $index . ' ' . $part . '<br/>';
			$test_path = $new_path . $part;
//echo $test_path . '<br/>';
//			echo "testing: " . $test_path . ".php<br/>";
			if (file_exists($test_path . '.php')) {
//echo "Controller found at: " . $test_path . "<br/>";
				$this->class_name = $part;
				$match = true;
				$this->url_parts = array_slice($parts, $index + 1);
				$new_path = $test_path . '.php';
				break;
			}
			elseif (is_dir($test_path)) {
//				echo "IsDir: " . $new_path . "<br/>";
				$new_path = $test_path . '/';
				$this->controller_path = $new_path;
			}
			else {
				$match_fail = true;
//				echo "Failure<br/>";
				break;
			}
		}

		if ($match_fail) {
			echo "No controller found :-(<br/>";
			return false;
		}
		elseif (!$match) {
			$new_path = $new_path . '/' . $index_class . '.php';
			if (file_exists($new_path)) {
				$this->class_name = $index_class;
				$match = true;
			}
		}

		if ($match) {
			$this->class_file = $new_path;
//			echo "Controller " . $new_path . " found<br/>";
		}
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
		
		if (!isset($_ns))
			$_ns = '\\Wibbler\\User\\Modules';
		$this->full_class_name = $_ns . "\\" . $this->class_name;

		if (class_exists($this->full_class_name)) {
			$controller = new $this->full_class_name(true);
			return $controller;
		}
		else {
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

		if (!empty($this->url_parts[0])) {
			$this->class_method = $this->url_parts[0];
			$this->url_parts = array_slice($this->url_parts, 1);
		}
		else
			$this->class_method = $index_method;
		$method = $this->class_method;
//echo "<strong>" . $method . "</strong><br/>";
		if (method_exists($this->controller, $method)) {
//			$this->controller->$method();
			// Create some reflection to confirm parameter counts are ok
			
			// Start reflecting the class
			$class = new \ReflectionClass($this->full_class_name);
			// Get the method details
			$method = $class->getMethod($method);
			// Find the number of required parameters
			$required_params = $method->getNumberOfRequiredParameters();

			// If the number of required parameters is greater than the number given
			if ($required_params > count($this->url_parts)) {
				// Set an error
				$this->error = 'Not enough parameters';
				return;
			}

			return true;
		}
		else {
			$this->error = 'Method not found';
		}
	}
}