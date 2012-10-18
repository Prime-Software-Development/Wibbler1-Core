<?php
namespace Wibbler;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dependency container for dependency injection
 */
class WibblerDependencyContainer {

	private $_instances = array();
	private $_params = array();

	function __construct($params) {
		$this->_params = $params;
	}

	public function getConfig($module) {

		$file = APPPATH . 'config/' . $module . '.php';
		
		if (!file_exists($file)) {
			throw new \Exception('Config file not found');
			return false;
		}
		include($file);

		return $config;
	}
}