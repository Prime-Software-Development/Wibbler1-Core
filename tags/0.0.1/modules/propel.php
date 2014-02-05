<?php
namespace Wibbler\Modules;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('propel/Propel.php');

class propel {

	function __construct(\Wibbler\WibblerDependencyContainer $dependencies)
	{
		$this->_config = $dependencies->getConfig('propel');

		\Propel::init($this->_config['conf_dir']);

		// Add the generated 'classes' directory to the include path
		set_include_path($this->_config['class_dir'] . PATH_SEPARATOR . get_include_path());
	}
}
