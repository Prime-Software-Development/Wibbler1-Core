<?php
namespace Wibbler\Modules;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class input {
	function __construct() {
	}

	function get($variable, $default = null) {
		if (isset($_GET[$variable]))
			return $_GET[$variable];
		else
			return $default;
	}

	function post($variable, $default = null) {
		if (isset($_POST[$variable]))
			return $_POST[$variable];
		else
			return $default;
	}
}
