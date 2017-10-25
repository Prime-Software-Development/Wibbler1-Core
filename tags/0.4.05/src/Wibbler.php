<?php
namespace Trunk\Wibbler;
use Trunk\Wibbler\Modules\config;
use TrunkSoftware\Component\Http\Request;

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
			$this->dependency_manager->getModule( "config" );

			// Create a WibblerLoader which loads the actual controller
			$wibbler_loader = $this->dependency_manager->getService( 'router.service' );
			if ( $wibbler_loader === false ) {
				$this->Show404( "Please specify a router service under router.service in the config file" );
				die();
			}
			$response = $wibbler_loader->handleRequest( $this->getRequest(), [] );

			if ( $response !== false ) {

			}
		} catch ( \Exception $ex ) {
			echo $ex->getMessage();
		}
	}

	/**
	 * Gets a request object
	 * @return Request
	 */
	public function getRequest() {
		$post_array = $_POST;
		// If there are no posted variables
		if ( $post_array == [] ) {
			// Get the contents of the php input stream (this will contain any raw data)
			$request_body = file_get_contents('php://input');

			// If there is some content
			if ( !empty($request_body) ) {
				// JSON decode it
				$post_array = json_decode($request_body, true);
			}
		}

		return new Request($_GET, $post_array, []);
	}

	function Show404( $message = null ) {
		header( "HTTP/1.0 404 Not Found" );
		echo $message;
		die();
	}
}