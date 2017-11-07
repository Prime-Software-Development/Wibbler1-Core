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
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function __construct() {

		// Keep a note of the dependency manager
		$this->_dependencies = WibblerDependencyContainer::Instance();

		// Load the configuration loading module
		$this->load_module( "config" );

		// Get the autoload config
		$this->_autoload = $this->config->load( 'autoload' );// $this->_dependencies->getConfig( 'autoload' );

		// If there are modules to load
		if ( isset( $this->_autoload[ 'modules' ] ) ) {
			// Go through the modules to autoload
			foreach ( $this->_autoload[ 'modules' ] as $module ) {
				// And load them (default namespace)
				$this->load_module( $module );
			}
		}

		// If there are helpers to load
		if ( isset( $this->_autoload[ 'helpers' ] ) ) {
			// Go through the helpers to autoload
			foreach ( $this->_autoload[ 'helpers' ] as $helper ) {
				// And load them
				$this->load_helper( $helper );
			}
		}
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