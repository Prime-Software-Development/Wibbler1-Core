<?php
namespace Wibbler;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WibblerController {
	private $_dependencies;

	function __construct() {
		$this->_dependencies = new WibblerDependencyContainer(null);
	}

	/**
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function init() {
		$this->_autoload = $this->_dependencies->getConfig('autoload');

		foreach ($this->_autoload['modules'] as $module)
			$this->_load_module($module, Wibbler::CORE);
		foreach ($this->_autoload['helpers'] as $helper)
			$this->_load_helper($helper, Wibbler::CORE);

	}

	/**
	 * Actually load the module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 * @param type $module_type Type of the module to load - either CORE or USER
	 */
	private function _load_module($module, $module_type) {
		$file_path = __dir__ . '/' . ($module_type == Wibbler::CORE ? 'modules/' : '../application/modules/') . $module . '.php';

		if (file_exists($file_path)) {
			include_once($file_path);

			$ns_extra = "\\Wibbler\\" . ($module_type == Wibbler::CORE ? 'Modules' : 'User') . "\\" . $module;
			$this->$module = new $ns_extra($this->_dependencies);
		}
	}

	/**
	 * Actually load the helper
	 * @param string $helper Name of the helper file to load
	 * @param type $helper_type Type of the helper to load - either CORE or USER
	 */
	private function _load_helper($helper, $helper_type) {
		$file_path = __dir__ . '/' . ($helper_type == Wibbler::CORE ? 'helpers/' : '../application/helpers/') . $helper . '.php';

		if (file_exists($file_path)) {
			include_once($file_path);
		}
	}


	/**
	 * Load a user module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 */
	public function load_module($module) {
		$this->_load_module($module, Wibbler::USER);
	}

	/**
	 * Load a user helper file
	 * @param string $helper Name of the helper file to load
	 */
	public function load_helper($helper) {
		$this->_load_helper($helper, Wibbler::USER);
	}
}