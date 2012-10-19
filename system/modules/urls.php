<?php
namespace Wibbler\Modules;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class urls {

	/**
	 * @var string Request type (http or https)
	 */
	var $http = '';
	/**
	 * @var string URL used to access the root of the system
	 */
	var $root_url = '';
	/**
	 * @var string Name of the server
	 */
	var $server_name = '';

	// Constructor of the core urls module
	function __construct() {

		$this->http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
		$this->server_name = $_SERVER['SERVER_NAME'];
		$this->root_url = $this->_get_current_url();
	}

	/**
	 * Gets the current url for the root of the system
	 * @return string
	 */
	private function _get_current_url() {
		$requested = $_SERVER['REQUEST_URI'];
		$script = $_SERVER['SCRIPT_NAME'];
		$result = '';

		// If index.php is in the request - then we aren't using htaccess rewrites
		// Therefore find the path quickly from the request
		if (strpos($requested, "index.php") > 0) {
			$result = substr($requested, 0, strpos($requested, "index.php") + 9) . '/';
			return $result;
		}

		// No index.php found in request - therefore we are using htaccess rewrites
		// Therefore find the path by comparing request and current script until they no longer match

		// Find the maximum number of characters to compare
		$requested_len = strlen($requested);
		$max_chars = strlen($script) < $requested_len ? strlen($script) : $requested_len;

		// Iterate over both strings
		for ($i = 0; $i < $max_chars; $i++) {
			if ($requested[$i] == $script[$i]) {
				$result .= $requested[$i];
			}
			else {
				break;
			}
		}
		
		if (strrpos($result, "/") != strlen($result)) {
			$result = substr($result, 0, strrpos($result, "/") + 1);
		}
		return $result;
	}

	public function redirect($url) {
		header('Location: ' . $this->root_url . $url);
	}
}