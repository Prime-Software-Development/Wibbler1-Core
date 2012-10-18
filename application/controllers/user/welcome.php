<?php
namespace Wibbler\User\Modules;
$_ns = '\\Wibbler\\User\\Modules';

class Welcome extends \Wibbler\WibblerController {

	function index($name) {
		echo "Welcome!" . $name;
	}
}