<?php
namespace Wibbler\User\Modules;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\Wibbler\\User\\Modules';

class Welcome extends \Wibbler\WibblerController {

	function index() {
		$data = array('PageTitle' => 'Hello world');

		$this->twig->display('welcome.twig', $data);
	}

	public function fred($banana) {
		echo "Hello fred " . $banana;
	}
}