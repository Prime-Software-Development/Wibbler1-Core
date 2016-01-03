<?php
namespace Trunk\Tinc;
require_once(__dir__ . '/BaseController.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\Trunk\\Tinc';

class Welcome extends \Trunk\Tinc\BaseController {

	var $bl_bypass_security = true;

	function index() {

		$action = $this->input->post('login_action');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		if ($action == '')
			$action = $this->input->get( 'login_action' );

		$this->data = array();

		switch ($action) {
			case 'login':

				$user = \Database\UserQuery::create()
					->filterByUserType( "Admin" )
					->useContactDetailRelatedByEmailContactDetailIdQuery()
						->filterByContactDetail( $username )
					->endUse()
					->filterByUserpassword( md5( $password ) );
				$user = $user->findOne();

				if (isset($user)) {
					$_SESSION["user_id"] = $user->getId();
					// Update the session expiration time
					$expires = new \DateTime("now +" . $this->session_length . " mins");
					$_SESSION['expires'] = $expires;

					// Set the default redirect path
					$redirect_path = 'user/';
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

				$this->data['message'] = 'Your username or password did not match any existing records.';
				break;
			case 'logout':
				$_SESSION["user_id"] = null;
				break;
		}


		$this->ShowTwig('welcome.twig');
	}
}