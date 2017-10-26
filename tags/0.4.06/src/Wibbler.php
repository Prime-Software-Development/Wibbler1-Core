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

	function __construct( $additional_config = null )
	{

		try {
			// Keep a note of the dependency manager
			// Dependency manager now auto-loads the config from config.yml file
			$this->dependency_manager = WibblerDependencyContainer::Instance( $additional_config );

			// Create a router service which loads the actual controller
			$router_service = $this->dependency_manager->getService( 'router.service' );

			// If there is no router service
			if ( $router_service === false ) {
				// Warn the user and exit
				$this->Show404( "Please specify a router service under router.service in the config file" );
				die();
			}
			// Get the response from the router
			$response = $router_service->handleRequest( $this->getRequest(), [] );

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