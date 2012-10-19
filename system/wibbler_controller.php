<?php
namespace Wibbler;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WibblerController {
	private $_dependencies;

	function __construct() {
		global $dependencies;
		$this->_dependencies = $dependencies;

		$this->init();
	}

	/**
	 * Initiate the controller - called after construction by the main Wibbler class
	 */
	function init() {
		$this->_autoload = $this->_dependencies->getConfig('autoload');

		foreach ($this->_autoload['modules'] as $module)
			$this->$module = $this->_dependencies->getModule($module);
		foreach ($this->_autoload['helpers'] as $helper)
			$this->_dependencies->getHelper($helper);
	}

	/**
	 * Load a user module - the file name and the class name must be identical
	 * @param string $module Name of the module to load
	 */
	public function load_module($module) {
		$this->$module = $this->_dependencies->getModule($module);
	}

	/**
	 * Load a user helper file
	 * @param string $helper Name of the helper file to load
	 */
	public function load_helper($helper) {
		$this->_dependencies->getHelper($helper);
	}
}