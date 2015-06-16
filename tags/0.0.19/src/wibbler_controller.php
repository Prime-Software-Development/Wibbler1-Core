<?php
namespace Trunk\Wibbler;
if ( defined( PROPEL_INC ) ) {
	\Propel::init( COMMONPATH . 'propel/build/conf/' . PROPEL_INC );
}
else {
	require_once COMMONPATH . '/propel/generated-conf/config.php';
}

class WibblerController {
	private $_dependencies;
	public $controller_path;

	/**
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function __construct( ) {

		// Keep a note of the dependency manager
		$this->_dependencies = WibblerDependencyContainer::Instance();

		// Get the autoload config
		$this->_autoload = $this->_dependencies->getConfig( 'autoload' );

		// Go through the modules to autoload
		foreach ( $this->_autoload[ 'modules' ] as $module ) {
			// And load them
			$this->load_module( $module );
		}
	}

	/**
	 * Load a user module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 */
	public function load_module($module, $namespace = null) {
		$this->$module = $this->_dependencies->getModule( $module, $namespace);
	}

	/**
	 * Load a user helper file
	 * @param string $helper Name of the helper file to load
	 */
	public function load_helper($helper) {
		$this->_dependencies->getHelper($helper);
	}
}