<?php
namespace MyApp;
require_once(__dir__ . '/BaseController.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\MyApp';

class Welcome extends \MyApp\BaseController {

	function index() {
		$data = array('PageTitle' => 'Hello world');

		$this->ShowTwig('welcome.twig', $data);
	}

	public function fred($banana) {
		echo "Hello fred " . $banana;
	}
}