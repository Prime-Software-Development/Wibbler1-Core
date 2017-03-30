<?php
namespace Trunk\Wibbler;
use Trunk\Wibbler\Modules\config;

if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

/**
 * Main class which creates all others
 */
class Wibbler {

	const CORE = 'CORE';
	const USER = 'USER';

	/**
	 * @var null|WibblerDependencyContainer
	 */
	private $dependency_manager;

	/**
	 * @var config
	 */
	private $config;

	function __construct( $additional_config = null ) {

		try {
			// Keep a note of the dependency manager
			// Dependency manager now auto-loads the config from config.php file
			$this->dependency_manager = WibblerDependencyContainer::Instance( $additional_config );

			// Create a WibblerLoader which loads the actual controller
			$wibbler_loader = WibblerLoader::Instance();
			$main_controller = $wibbler_loader->controller;

			if ( $wibbler_loader->error !== false || $main_controller === null ) {
				$this->Show404( $wibbler_loader->error );
			}

			call_user_func_array( array( $main_controller, "pre_function_call" ), [ $wibbler_loader->class_method, $wibbler_loader->method_docblock ] );
			call_user_func_array( array( $main_controller, $wibbler_loader->class_method ), $wibbler_loader->url_parts );
		}
		catch ( \Exception $ex ) {
			echo $ex->getMessage();
		}
	}

	function Show404( $message = null ) {
		header( "HTTP/1.0 404 Not Found" );
		echo $message;
		die();
	}
}