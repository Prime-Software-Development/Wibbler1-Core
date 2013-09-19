<?php
namespace MyApp;
require_once(__dir__ . '/BaseController.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\MyApp';

class Welcome extends \MyApp\BaseController {

	function index() {

		$action = $this->input->post('login_action');

		switch ($action) {
			case 'login':
				//$_SESSION
				break;
		}


		$data = array('PageTitle' => 'Hello world');

		$this->ShowTwig('welcome.twig', $data);
	}
}