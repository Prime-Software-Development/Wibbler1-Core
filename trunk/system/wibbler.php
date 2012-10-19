<?php
namespace Wibbler;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(__dir__ . '/wibbler_loader.php');
require_once(__dir__ . '/wibbler_controller.php');
require_once(__dir__ . '/wibbler_dependency_container.php');

/**
 * Main class which creates all others
 */
class Wibbler {

	const CORE = 'CORE';
	const USER = 'USER';

	function __construct() {
		global $load_modules;
		global $load_helpers;

		try {
			$b = new WibblerLoader();
			if ($b->error !== false) {
				$this->Show404($b->error);
			}

			$main_controller = $b->controller;
			if (method_exists($main_controller, 'init')) {
				$main_controller->init();
				call_user_func_array(array($main_controller, $b->class_method), $b->url_parts);
			}
			else {
				throw new Exception('Class does not inherit from controller');
			}
		}
		catch (Exception $ex) {
			echo $ex->getMessage();
		}
	}

	function Show404($message = null) {
		header("HTTP/1.0 404 Not Found");
		echo $message;
		die();
	}
}

// Create a new dependency injection container
$dependencies = new WibblerDependencyContainer(null);

// Initiate the system
new \Wibbler\Wibbler();