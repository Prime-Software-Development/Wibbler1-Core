<?php
namespace Trunk\Wibbler;
if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );
include_once( __DIR__ . "/base.php" );

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
	 * The instance of this object
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Private constructor - stops creation of this without using Instance (below)
	 */
	private function __construct() {
	}

	public static function Instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new WibblerDependencyContainer();
		}
		return self::$_instance;
	}

	public function getModule( $module, $namespace = null, $option = null ) {

		if ( isset( $this->_modules[ $module ] ) )
			return $this->_modules[ $module ];

		$this->_modules[ $module ] = $this->_load_module( $module, $namespace, $option );
		return $this->_modules[ $module ];
	}

	public function getHelper( $helper ) {

		if ( isset( $this->_modules[ $helper ] ) )
			return $this->_modules[ $helper ];

		$this->_modules[ $helper ] = $this->_load_helper( $helper );
		return $this->_modules[ $helper ];
	}

	/**
	 * Actually load the module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 */
	private function _load_module( $module, $namespace = null, $option = null ) {
		if ( $namespace == null )
			$namespace = "\\Trunk\\Wibbler\\Modules\\";

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

}