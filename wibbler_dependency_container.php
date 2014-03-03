<?php
namespace Trunk\Wibbler;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dependency container for dependency injection
 */
class WibblerDependencyContainer {

	private $_instances = array();
	private $_modules = array();

	function __construct() {
	}

	public function getConfig($module) {

		$file = COMMONPATH . 'config/' . $module . '.php';
		
		if (!file_exists($file)) {
			throw new \Exception('Config file not found');
			return false;
		}
		include($file);

		return $config;
	}

	public function getModule($module, $namespace) {

		if (isset($this->_modules[$module]))
			return $this->_modules[$module];

		$this->_modules[$module] = $this->_load_module($module, $namespace);
		return $this->_modules[$module];
	}

	public function getHelper($helper) {

		if (isset($this->_modules[$helper]))
			return $this->_modules[$helper];

		$this->_modules[$helper] = $this->_load_module($helper);
		return $this->_modules[$helper];
	}

	/**
	 * Actually load the module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 */
	private function _load_module( $module, $namespace = null ) {
		if ( $namespace == null )
			$namespace = "\\Trunk\\Wibbler\\Modules\\";

		$core_file_path = __dir__ . '/modules/' . $module . '.php';
		$user_file_path = COMMONPATH . '/modules/' . $module . '.php';

		$file_path = false;
		if (file_exists($core_file_path))
			$file_path = $core_file_path;
		elseif (file_exists($user_file_path))
			$file_path = $user_file_path;

		if ($file_path !== false) {
			include_once($file_path);

			$ns_extra = $namespace . $module;
			return new $ns_extra($this);
		}

		return false;
	}

	/**
	 * Actually load the helper
	 * @param string $helper Name of the helper file to load
	 */
	private function _load_helper($helper) {
		$core_file_path = __dir__ . '/helpers/' . $helper . '.php';
		$user_file_path = COMMONPATH . '/helpers/' . $helper . '.php';

		$file_path = false;
		if (file_exists($core_file_path))
			$file_path = $core_file_path;
		elseif (file_exists($user_file_path))
			$file_path = $user_file_path;

		if ($file_path !== false) {
			include_once($file_path);
		}
	}

}