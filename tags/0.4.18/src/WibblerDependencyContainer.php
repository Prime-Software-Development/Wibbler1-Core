<?php
namespace Trunk\Wibbler;
if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

/**
 * Dependency container for dependency injection
 */
final class WibblerDependencyContainer {

	/**
	 * Cache of the modules which have been loaded
	 * @var array
	 */
	private $_modules = array();

	/**
	 * Cache of services which have been loaded
	 * @var array
	 */
	private $services;
	private $services_config;

	/**
	 * The instance of this object
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Private constructor - stops creation of this without using Instance (below)
	 */
	private function __construct( $additional_config = null ) {
		$this->services = [];
		$this->services_config = [];

		// Load the configuration loading module
		$this->config = $this->getModule( "config" );

		$config_file = "config";

		// Get the main config file
		$this->config->load( $config_file );

		// Add the additional configuration options (if set)
		$this->config->add_from_array( $additional_config );
		$this->config->add_from_array( [ "additional_configs" => ( $additional_config !== null ) ] );
		//$this->config->add_from_array( [ "services" => [ 'wibbler.loader' => [ 'class' => '\Trunk\Wibbler\Services\CoreRouter' ] ] ] );

		// Get the autoload config
		$loaded_config = $this->config->getConfig( 'config' );
		if ( isset( $loaded_config[ 'services' ] ) ) {
			$this->setAdvancedServiceConfig( $loaded_config[ 'services' ] );
		}
	}

	/**
	 * @param null $additional_config
	 * @return null|WibblerDependencyContainer
	 */
	public static function Instance( $additional_config = null ) {
		if ( self::$_instance === null ) {
			self::$_instance = new WibblerDependencyContainer( $additional_config );
		}
		return self::$_instance;
	}

	/**
	 * Returns the module or attempts to load if not already loaded
	 * @param      $module
	 * @param null $namespace
	 * @param null $option
	 * @return mixed
	 */
	public function getModule( $module, $namespace = null, $option = null ) {

		if ( isset( $this->_modules[ $module ] ) )
			return $this->_modules[ $module ];

		$this->_modules[ $module ] = $this->_load_module( $module, $namespace, $option );
		return $this->_modules[ $module ];
	}

	/**
	 * Returns the helper or attempts to load if not already loaded
	 * @param $helper
	 * @return mixed
	 */
	public function getHelper( $helper ) {

		if ( isset( $this->_modules[ $helper ] ) )
			return $this->_modules[ $helper ];

		$this->_modules[ $helper ] = $this->_load_helper( $helper );
		return $this->_modules[ $helper ];
	}

	/**
	 * Actually load the module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 * @param null   $namespace
	 * @param null   $option
	 * @return bool
	 */
	private function _load_module( $module, $namespace = null, $option = null ) {
		if ( $namespace == null )
			$namespace = "\\Trunk\\Wibbler\\Modules\\";

		// If the class exists (it should if using composer)
		if ( class_exists( $namespace . $module ) ) {
			// Set the namespace
			$ns_extra = $namespace . $module;

			// We are using a singleton pattern for this module
			return $ns_extra::Instance( $option );
		}

		$core_file_path = __dir__ . '/modules/' . $module . '.php';
		$user_file_path = COMMONPATH . '/modules/' . $module . '.php';

		$file_path = false;
		if ( file_exists( $core_file_path ) )
			$file_path = $core_file_path;
		elseif ( file_exists( $user_file_path ) )
			$file_path = $user_file_path;

		if ( $file_path !== false ) {
			include_once( $file_path );

			$ns_extra = $namespace . $module;

			// We are using a singleton pattern for this module
			return $ns_extra::Instance( $option );
		}

		return false;
	}

	/**
	 * Actually load the helper
	 * @param string $helper Name of the helper file to load
	 */
	private function _load_helper( $helper ) {
		$core_file_path = __dir__ . '/helpers/' . $helper . '.php';
		$user_file_path = COMMONPATH . '/helpers/' . $helper . '.php';

		$file_path = false;
		if ( file_exists( $core_file_path ) )
			$file_path = $core_file_path;
		elseif ( file_exists( $user_file_path ) )
			$file_path = $user_file_path;

		if ( $file_path !== false ) {
			include_once( $file_path );
		}
	}

	/**
	 * @param $service_id
	 * @return bool
	 */
	public function getService( $service_id ) {
		// service not yet loaded
		if ( !isset( $this->services[ $service_id ] ) ) {
			// service doesn't exists in the config
			if ( !isset( $this->services_config[ $service_id ] ) ) {
				return false;
			}

			// service class
			$data = $this->services_config[ $service_id ];
			if ($data[ 'class' ] == '' )
				return false;

			$arguments = isset( $data[ 'args' ] ) ? $data[ 'args' ] : null;
			$reflect = new \ReflectionClass( $data[ 'class' ] );

			if ( $arguments ) {
				$service = $reflect->newInstance( $arguments );
			}
			else {
				$service = $reflect->newInstance();
			}

			// instantiate service
			$this->services[ $service_id ] = $service;
		}

		return $this->services[ $service_id ];
	}

	/**
	 * Set services config
	 *
	 * @param array $config
	 * @throws \Exception
	 */
	public function setServiceConfig( array $config ) {
		$this->_setServiceConfig( $config, array() );
	}

	/**
	 * Merge existing services config with the given config
	 *
	 * @param array $config
	 *
	 * @throws \Exception
	 */
	public function addServiceConfig( array $config ) {
		$this->_setServiceConfig( $config, $this->services_config );
	}

	protected function _setServiceConfig( array $config, array $services ) {
		// check for duplicate service ids
		foreach ( $config as $service ) {
			$service_id = $service[ 'id' ];

			if ( isset( $services[ $service_id ] ) ) {
				throw new \Exception( "Duplicate Service Id found while loading Service config" );
			}

			$services[ $service_id ] = $service;
		}

		$this->services_config = $services;
	}

	public function setAdvancedServiceConfig( array $config ) {
		$this->_setAdvancedServiceConfig( $config, array() );
	}

	protected function _setAdvancedServiceConfig( array $config, array $services ) {
		// check for duplicate service ids
		foreach ( $config as $service_id => $service ) {

			if ( isset( $services[ $service_id ] ) ) {
				throw new \Exception( "Duplicate Service Id found while loading Service config" );
			}

			$services[ $service_id ] = $service;
		}

		$this->services_config = $services;
	}
}