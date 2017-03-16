<?php
namespace Trunk\Wibbler;
if ( defined( "PROPEL_INC" ) ) {
	\Propel::init( COMMONPATH . 'propel/build/conf/' . PROPEL_INC );
	// Add the generated 'classes' directory to the include path
	set_include_path( COMMONPATH . "propel/build/classes" . PATH_SEPARATOR . get_include_path());
} else {
	require_once COMMONPATH . '/propel/generated-conf/config.php';
}

class WibblerController {

	/**
	 * Holds the dependency container
	 * @var null|WibblerDependencyContainer
	 */
	private $_dependencies;

	/**
	 * Holds the full URL path to this controller
	 * @var
	 */
	protected $controller_path;

	/**
	 * Holds an array of the URL path to the controller
	 * @var array
	 */
	protected $controller_path_parts = [ ];

	/**
	 * @var \Trunk\Wibbler\Modules\Config
	 */
	protected $config;

	/**
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function __construct() {

		// Keep a note of the dependency manager
		$this->_dependencies = WibblerDependencyContainer::Instance();

		// Load the configuration loading module
		$this->load_module( "config" );

		// Get the autoload config
		$_config_autoload = $this->config->load( 'autoload' );// $this->_dependencies->getConfig( 'autoload' );
		// Get the services config
		$_config_services = $this->config->load( 'services' );


		// If there are no services defined in the services config file
		if ( $_config_services == [] ) {
			// If there are services in the autoload config file
			if ( $_config_autoload[ 'services' ] ) {
				// Use the services in the autoload config file
				$_config_services = [ 'services' => $_config_autoload[ 'services' ] ];
				// If there are auto-load services
				if ( isset( $_config_autoload[ 'autoload_services' ] ) ) {
					// Use the auto-load services
					$_autoload_services = $_config_autoload[ 'autoload_services' ];
				}
			}
		}
		else {
			// If there are auto-load services
			if ( isset( $_config_autoload[ 'services' ] ) ) {
				// Use the auto-load services
				$_autoload_services = $_config_autoload[ 'services' ];
			}
		}

		// If there are any services configured
		if ( isset( $_config_services[ 'services' ] ) ) {
			$services = $_config_services[ 'services' ];
			// Set the services config
			$this->_dependencies->setServiceConfig( $services );

			// If there are auto-load services
			if ( isset( $_autoload_services ) ) {
				// Iterate over them
				foreach( $_autoload_services as $service_name ) {
					// Get the service
					$this->$service_name = $this->_dependencies->getService( $service_name );
				}
			}
		}

		// If there are modules to load
		if ( isset( $_config_autoload[ 'modules' ] ) ) {
			// Go through the modules to autoload
			foreach ( $_config_autoload[ 'modules' ] as $module ) {
				// And load them (default namespace)
				$this->load_module( $module );
			}
		}

		// If there are helpers to load
		if ( isset( $_config_autoload[ 'helpers' ] ) ) {
			// Go through the helpers to autoload
			foreach ( $_config_autoload[ 'helpers' ] as $helper ) {
				// And load them
				$this->load_helper( $helper );
			}
		}
	}

	/**
	 * Function called after the constructor, but before the main method
	 * This function will know the method being called and it's doc block
	 */
	public function pre_function_call( $method, $docblock = null ) {
	}

	/**
	 * Load a user module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 */
	public function load_module( $module, $namespace = null, $option = null ) {
		$this->$module = $this->_dependencies->getModule( $module, $namespace, $option );
	}

	/**
	 * Load a user helper file
	 * @param string $helper Name of the helper file to load
	 */
	public function load_helper( $helper ) {
		$this->_dependencies->getHelper( $helper );
	}

	/**
	 * Sets the path and parts of the path to the controller
	 * @param $controller_path
	 * @param $controller_path_parts
	 */
	public function _set_controller_details( $controller_path, $controller_path_parts ) {
		$this->controller_path = $controller_path;
		$this->controller_path_parts = $controller_path_parts;
	}
}