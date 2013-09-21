<?php
namespace MyApp;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\MyApp';

class BaseController extends \Wibbler\WibblerController {

	/**
	 * Constructor
	 */
	function __construct() {

		parent::__construct();

		// If session doesn't exist - start one
		if (!isset($_SESSION))
			session_start();
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
	private function _GenerateTwig($template, $data) {
		$data['system']['paths']['root'] = $this->urls->root_url;
		$data['system']['paths']['resources'] = $this->urls->root_url . 'resources/';
		$data['system']['paths']['css'] = $this->urls->root_url . 'resources/css/';
		$data['system']['paths']['jscript'] = $this->urls->root_url . 'resources/js/';

		return $this->twig->render($template, $data);
	}

	/**
	 * Renders the twig template using the data passed to it returning the html
	 * @param type $template
	 * @param type $data
	 */
	function RenderTwig($template, $data) {
		return $this->_GenerateTwig($template, $data);
	}

	/**
	 * Displays the twig template using the data passed to it
	 * @param type $template
	 * @param type $data
	 */
	function ShowTwig($template, $data) {
		echo $this->_GenerateTwig($template, $data);
	}
}