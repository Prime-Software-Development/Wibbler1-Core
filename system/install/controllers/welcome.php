<?php
namespace MyApp;
require_once(__dir__ . '/BaseController.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\MyApp';

class Welcome extends \MyApp\BaseController {

	$users = array(
		array("id" => 1, "username" => "admin", "password" => "password")
	);

	function index() {

		$action = $this->input->post('login_action');
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		switch ($action) {
			case 'login':

				foreach ($users as $user) {
					if ($username == $user["username"] && $password = $user["password"]) {
						$_SESSION["user_id"] = $user["id"];

						// Set the default redirect path
						$redirect_path = '/dashboard/';
						// If there is a session variable with the calling url in it
						if (isset($_SESSION['calling_url'])) {
							// Change where to redirect to so the user gets to where they wanted
							$redirect_path = '/' . $_SESSION['calling_url'];
							// Clear the session variable
							unset($_SESSION['calling_url']);
						}

						// Redirect to the home page or the calling url
						$this->urls->redirect($redirect_path);
						exit();
					}
				}
				break;
			case 'logout':
				$_SESSION["user_id"] = null;
				break;
		}


		$data = array('PageTitle' => 'Hello world');

		$this->ShowTwig('welcome.twig', $data);
	}
}