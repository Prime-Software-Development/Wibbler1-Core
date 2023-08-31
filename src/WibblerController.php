<?php
namespace Trunk\Wibbler;

class WibblerController {
	/**
	 * @var WibblerDependencyContainer
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

	protected $request;

	/**
	 * Holds loaded helpers
	 * @var array
	 */
	private $__helpers = [];
	/**
	 * Holds loaded modules
	 * @var array
	 */
	private $__modules = [];
	/**
	 * Holds loaded services
	 * @var array
	 */
	private $__services = [];

	/**
	 * @var array
	 */
	public $url_parts = [];

	/**
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function __construct() {
		$this->_load_configs();
	}

	/**
	 * Whether security checks were passed or not
	 */
	private $security_check_result = true;

	/**
	 * @return bool
	 */
	public function getSecurityPassed() {
		return $this->security_check_result === true;
	}

	/**
	 * @return mixed
	 */
	public function getSecurityCheckResult() {
		return $this->security_check_result;
	}

	/**
	 * @param $security_check_result
	 */
	protected function setSecurityPassed( $security_check_result ) {
		$this->security_check_result = $security_check_result;
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
		$this->__modules[ $module ] = $this->_dependencies->getModule( $module, $namespace, $option );
		return $this->__modules[ $module ];
	}

	/**
	 * Load a user helper file
	 * @param string $helper Name of the helper file to load
	 */
	public function load_helper( $helper ) {
		$this->__helpers[ $helper ] = $this->_dependencies->getHelper( $helper );
		return $this->__helpers[ $helper ];
	}

	/**
	 * Load a user service
	 * @param $service_name
	 * @return mixed
	 */
	public function load_service( $service_name ) {
		$this->__services[ $service_name ] = $this->_dependencies->getService( $service_name );
		return $this->__services[ $service_name ];
	}

	/**
	 * Sets the path and parts of the path to the controller
	 * @param $additional_config
	 * @param $controller_path
	 * @param $controller_path_parts
	 */
	public function _set_controller_details( $controller_path, $controller_path_parts ) {
		$this->controller_path = $controller_path;
		$this->controller_path_parts = $controller_path_parts;
	}

	/**
	 * Sets the request object
	 * @param $request
	 */
	public function set_request( $request ) {
		$this->request = $request;
	}
	/**
	 * Uses the loaded config to autoload modules, helpers and services as required
	 */
	private function _load_configs() {

		// Keep a note of the dependency manager
		$this->_dependencies = WibblerDependencyContainer::Instance();

		// Load the configuration loading module
		$this->config = $this->load_module( "config" );

		// Get the autoload config
		$loaded_config = $this->config->getConfig( 'config' );

		// If there are modules to load
		if ( isset( $loaded_config[ 'autoload' ][ 'modules' ] ) ) {
			$this->___load_modules( $loaded_config[ 'autoload' ][ 'modules' ] );
		}
		// If there are helpers to load
		if ( isset( $loaded_config[ 'helpers' ] ) ) {
			$this->__load_helpers( $loaded_config[ 'helpers' ] );
		}

		// If there are modules to load
		if ( isset( $loaded_config[ 'autoload' ][ 'services' ] ) ) {
			$this->__load_services( $loaded_config[ 'autoload' ][ 'services' ] );
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
			$this->load_service( $service_name );
		}
	}

	/**
	 * Getter
	 * @param string $name
	 * @return mixed|void
	 */
	public function __get( string $name ) {
		if ( array_key_exists( $name, $this->__services ) ) {
			return $this->__services[ $name ];
		}
		if ( array_key_exists( $name, $this->__modules ) ) {
			return $this->__modules[ $name ];
		}
		if ( array_key_exists( $name, $this->__helpers ) ) {
			return $this->__helpers[ $name ];
		}
	}
}