<?php
namespace MyApp;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\MyApp';

class BaseController extends \Trunk\Wibbler\WibblerController {

	var $bl_bypass_security = false;

	/**
	 * Constructor
	 */
	function __construct() {

		parent::__construct();

		// If session doesn't exist - start one
		if (!isset($_SESSION))
			session_start();

		if (!$this->bl_bypass_security)
			$this->CheckUser();
	}

	/**
	 * Checks if the current user is logged in
	 */
	public function CheckUser()
	{
		$now = new \DateTime("now");

		// If the user id doesn't exist or the session has expired
		if (!isset($_SESSION['user_id']))
		{
			// Store the url the user has requested - once logged back in they can then be directed back
			$_SESSION['calling_url'] =  $this->urls->request_uri;

			// Redirect to the home page
			$this->url->redirect('/');

			// Stop running this code
			exit();
		}

		return true;
	}

	/**
	 * Generates the html to either render or display
	 * @param type $template
	 * @param type $data
	 * @return type
	 */
	private function _GenerateTwig($template) {
		$this->data['system']['paths']['controller'] = $this->urls->controller_path;
		$this->data['system']['paths']['resources'] = $this->urls->root_url . 'resources/';
		$this->data['system']['paths']['css'] = $this->urls->root_url . 'resources/css/';
		$this->data['system']['paths']['jscript'] = $this->urls->root_url . 'resources/js/';
		$this->data['system']['paths']['rdparty'] = $this->urls->root_url . 'resources/3rdparty/';

		return $this->twig->render($template, $this->data);
	}

	/**
	 * Renders the twig template using the data passed to it returning the html
	 * @param type $template
	 * @param type $data
	 */
	function RenderTwig($template) {
		return $this->_GenerateTwig($template);
	}

	/**
	 * Displays the twig template using the data passed to it
	 * @param type $template
	 * @param type $data
	 */
	function ShowTwig($template) {
		echo $this->_GenerateTwig($template);
	}

	/**
	 * Outputs the given array as a json string
	 * @param type $data
	 */
	function ShowJSON($data) {
		header("Content-Type: application/json"); // HTTP/1.1
		echo json_encode($data);
	}

	/**
	 * Outputs the message as part of a JSON array, setting status to Fail and exiting
	 * @param type $message
	 * @param type $extras
	 */
	function ShowJSONFail($message, $extras = null) {

		if ($extras == null)
			$extras = array();

		$extras['status'] = 'Fail';
		$extras['notes'] = $message;

		$this->ShowJSON($extras);
		exit();
	}
}
