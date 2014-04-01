<?php
namespace Trunk\Wibbler;
$_ns = "\\Trunk\\Wibbler";

/**
 * Main class which creates all others
 */
class Wibbler {

	const CORE = 'CORE';
	const USER = 'USER';

	function __construct() {
		global $load_modules;
		global $load_helpers;

		// Create a new dependency injection container
		$dependencies = new WibblerDependencyContainer( );

		try {
			$b = new WibblerLoader( $dependencies );
			if ($b->error !== false) {
				$this->Show404($b->error);
			}

			$main_controller = $b->controller;

			call_user_func_array(array($main_controller, $b->class_method), $b->url_parts);
		}
		catch (\Exception $ex) {
			echo $ex->getMessage();
		}
	}

	function Show404($message = null) {
		header("HTTP/1.0 404 Not Found");
		echo $message;
		die();
	}
}
