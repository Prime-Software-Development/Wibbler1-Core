<?php
namespace Trunk\Wibbler\Modules;

class input extends base {
	function __construct() {
	}

	/**
	 * Fetch an item from the GET array
	 * @param string|null $variable
	 * @param mixed $default
	 * @return mixed
	 */
	function get($variable = null, $default = null) {
		return $this->_fetch_from_array( $_GET, $variable, $default );
	}

	/**
	 * Fetch an item from the POST array
	 * @param string|null $variable
	 * @param mixed $default
	 * @return mixed
	 */
	function post($variable, $default = null) {
		return $this->_fetch_from_array( $_POST, $variable, $default );
	}

	/**
	 * Fetch an item from the SERVER array
	 * @param string|null $variable
	 * @param mixed $default
	 * @return mixed
	 */
	function server($variable, $default = null) {
		return $this->_fetch_from_array( $_SERVER, $variable, $default );
	}

	/**
	 * Returns the required variable from the given array, or the default if the value doesn't exist
	 * @param $array
	 * @param $variable
	 * @param $default
	 * @return mixed
	 */
	private function _fetch_from_array( &$array, $variable, $default ) {
		// If there is no variable
		if ( $variable == null ) {
			// Return the whole array
			return $array;
		}

		// If the variable exists
		if (isset($array[$variable])) {
			// Return the variable
			return $array[ $variable ];
		}
		else {
			// Return the default value
			return $default;
		}
	}

	/**
	 * Is ajax Request?
	 * Test to see if a request contains the HTTP_X_REQUESTED_WITH header
	 * @return 	boolean
	 */
	public function is_ajax_request()
	{
		return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
	}

	/**
	 * Is cli Request?
	 * Test to see if a request was made from the command line
	 * @return 	boolean
	 */
	public function is_cli_request()
	{
		return (bool) defined('STDIN');
	}
}