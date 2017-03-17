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
			#region Load the configuration here, before creating the controller
			// Keep a note of the dependency manager
			$this->dependency_manager = WibblerDependencyContainer::Instance();
			// Load the configuration loading module
			$this->config = $this->dependency_manager->getModule( "config" );

			$config_file = $additional_config === null ? "autoload" : "config";
			// Get the main config file
			$this->config->load( $config_file );
			// Add the additional configuration options (if set)
			$this->config->add_from_array( $additional_config );
			$this->config->add_from_array( [ "additional_configs" => ( $additional_config !== null ) ] );
			#endregion

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