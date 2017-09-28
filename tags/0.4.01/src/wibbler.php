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
	 * @var array
	 */
	private $config;

	function __construct( $additional_config = null )
	{

		try {
			// Keep a note of the dependency manager
			// Dependency manager now auto-loads the config from config.yml file
			$this->dependency_manager = WibblerDependencyContainer::Instance( $additional_config );

			/**
			 * @var $config_module \Trunk\Wibbler\Modules\config
			 */
			$config_module = $this->dependency_manager->getModule( "config" );
			$config_config = $config_module->getConfig( "config" );
			$router_service = 'wibbler.loader';
			if ( isset( $config_config[ 'router' ] ) ) {
				if ( isset( $config_config[ 'router' ][ 'service' ] ) ) {
					$router_service = $config_config[ 'router' ][ 'service' ];
				}
			}

			// Create a WibblerLoader which loads the actual controller
			$wibbler_loader = $this->dependency_manager->getService( $router_service );
			$main_controller = $wibbler_loader->getController();

			if ( $wibbler_loader->getError() !== false || $main_controller === null ) {
				$this->Show404( $wibbler_loader->error );
			}

			call_user_func_array( array( $main_controller, "pre_function_call" ), [ $wibbler_loader->class_method, $wibbler_loader->method_docblock ] );
			call_user_func_array( array( $main_controller, $wibbler_loader->class_method ), $wibbler_loader->url_parts );
		} catch ( \Exception $ex ) {
			echo $ex->getMessage();
		}
	}

	function Show404( $message = null ) {
		header( "HTTP/1.0 404 Not Found" );
		echo $message;
		die();
	}
}