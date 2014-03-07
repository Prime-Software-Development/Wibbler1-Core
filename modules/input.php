<?php
namespace Trunk\Wibbler\Modules;

class input {
	function __construct() {
	}

	function get($variable = null, $default = null) {
		if ( $variable == null )
			return $_GET;

		if (isset($_GET[$variable]))
			return $_GET[$variable];
		else
			return $default;
	}

	function post($variable, $default = null) {
		if ( $variable == null )
			return $_POST;

		if (isset($_POST[$variable]))
			return $_POST[$variable];
		else
			return $default;
	}
}
