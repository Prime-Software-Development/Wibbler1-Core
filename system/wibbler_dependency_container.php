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
		
	}
}