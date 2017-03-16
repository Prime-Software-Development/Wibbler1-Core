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
	 * Stores the additional configuration
	 * @var array
	 */
	private $additional_config;

	/**
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function __construct() {
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
	 * @param $additional_config
	 * @param $controller_path
	 * @param $controller_path_parts
	 */
	public function _set_controller_details( $additional_config, $controller_path, $controller_path_parts ) {
		$this->additional_config = $additional_config;
		$this->controller_path = $controller_path;
		$this->controller_path_parts = $controller_path_parts;

		$this->_load_configs();
	}

	private function _load_configs() {

		// Keep a note of the dependency manager
		$this->_dependencies = WibblerDependencyContainer::Instance();

		// Load the configuration loading module
		$this->load_module( "config" );

		if ( $this->additional_config ) {
			// Get the autoload config
			$loaded_config = $this->config->load( 'config' );

			foreach( $loaded_config[ 'services' ] as $service_id => $service ) {

				if ( isset( $this->additional_config[ 'config' ][ $service_id ] ) ) {
					$loaded_config[ 'services' ][ $service_id ][ 'args' ] = $this->additional_config[ 'config' ][ $service_id ];
				}
			}

			if ( isset( $this->_config[ 'modules' ] ) ) {
				$this->___load_modules( $this->_autoload[ 'modules' ] );
			}
			// If there are helpers to load
			if ( isset( $this->_config[ 'helpers' ] ) ) {
				$this->__load_helpers( $this->_config[ 'helpers' ] );
			}

			// if there are any services registered in the config
			// add them to the dependency container
			if ( isset( $this->_config[ 'services' ] ) ) {
				$this->_dependencies->setAdvancedServiceConfig( $this->_config[ 'services' ] );

				if ( isset( $this->_config[ 'autoload_services' ] ) ) {
					$this->__load_services( $this->_config[ 'autoload_services' ] );
				}
			}
		}
		else {
			// Get the autoload config
			$this->_autoload = $this->config->load( 'autoload' );

			// If there are modules to load
			if ( isset( $this->_autoload[ 'modules' ] ) ) {
				$this->___load_modules( $this->_autoload[ 'modules' ] );
			}

			// If there are helpers to load
			if ( isset( $this->_autoload[ 'helpers' ] ) ) {
				$this->__load_helpers( $this->_autoload[ 'helpers' ] );
			}

			// if there are any services registered in the config
			// add them to the dependency container
			if ( isset( $this->_autoload[ 'services' ] ) ) {
				$this->_dependencies->setServiceConfig( $this->_autoload[ 'services' ] );

				if ( isset( $this->_autoload[ 'autoload_services' ] ) ) {
					$this->__load_services( $this->_autoload[ 'autoload_services' ] );
				}
			}
		}
	}

	/**
	 * Load the required helpers
	 * @param $helpers
	 */
	private function __load_helpers( $helpers ) {
		// Go through the helpers to autoload
		foreach ( $helpers as $helper ) {
			// And load them
			$this->load_helper( $helper );
		}
	}

	/**
	 * Load the required modules
	 * @param $modules
	 */
	private function ___load_modules( $modules ) {
		// Go through the modules to autoload
		foreach ( $modules as $module ) {
			// And load them (default namespace)
			$this->load_module( $module );
		}
	}

	/**
	 * Load the required services
	 * @param $services
	 */
	private function __load_services( $services ) {
		foreach ( $services as $service_name ) {
			$this->$service_name = $this->_dependencies->getService( $service_name );
		}
	}
}