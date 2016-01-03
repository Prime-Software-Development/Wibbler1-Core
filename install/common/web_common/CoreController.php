<?php
namespace Trunk\Tinc;
$_ns = '\\Trunk\\Tinc';

class CoreController extends \Trunk\Wibbler\WibblerController {

	var $bl_bypass_security = false;
	var $session_length = 60;

	/**
	 * Constructor
	 */
	function __construct( $dependencies = null ) {

		parent::__construct( $dependencies );

		// If session doesn't exist - start one
		if (!isset($_SESSION))
			session_start();

		if (!$this->bl_bypass_security) {
			$this->CheckUser();
		}
		else {
			$this->current_user = null;
		}
	}

	/**
	 * Checks if the current user is logged in
	 */
	public function CheckUser()
	{
		$now = new \DateTime("now");

		// If the user id doesn't exist or the session has expired
		if ( !isset($_SESSION['user_id'])  || $_SESSION['expires'] < $now )
		{
			// Store the url the user has requested - once logged back in they can then be directed back
			$_SESSION['calling_url'] = $this->urls->get_requested_url();

			// Redirect to the home page
			$this->urls->redirect();

			// Stop running this code
			exit();
		}

		// Set the time the session expires
		$expires = new \DateTime("now +" . $this->session_length . " minutes");
		$_SESSION['expires'] = $expires;

		return true;
	}

	/**
	 * Generates the html to either render or display
	 * @param type $template
	 * @param type $data
	 * @return type
	 */
	private function _GenerateTwig($template) {
		$this->data[ 'system' ][ 'paths' ][ 'controller' ] = $this->urls->controller_path;
		$this->data[ 'system' ][ 'paths' ][ 'resources' ] = $this->urls->root_url . '';
		$this->data[ 'system' ][ 'paths' ][ 'css' ] = $this->urls->root_url . 'css/';
		$this->data[ 'system' ][ 'paths' ][ 'jscript' ] = $this->urls->root_url . 'js/';
		$this->data[ 'system' ][ 'paths' ][ 'rdparty' ] = $this->urls->root_url . '3rdparty/';
		$this->data[ 'system' ][ 'area' ] = SYSTEMAREA;
		$this->data[ 'system' ][ 'environment' ] = ENVIRONMENT;
		$this->data[ 'current_user' ] = $this->current_user;

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

	/**
	 * Outputs the message as part of a JSON array, setting status to OK and exiting
	 * @param string $message
	 * @param array $extras
	 */
	function ShowJSONSuccess( $message = "", $extras = null ) {

		if ( $extras == null )
			$extras = array();

		$extras[ 'status' ] = 'OK';
		$extras[ 'notes' ] = $message;

		$this->ShowJSON( $extras );
		exit();
	}

	/**
	 * Generate an excel file to the screen
	 * @param type $template_file
	 * @param type $data
	 * @param type $report_name
	 */
	protected function GenerateExcel($template_file, $report_name, $filename = null) {
		$html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html><head></head><body>' .
				$this->_GenerateTwig( $template_file ) .
				'</body></html>';
		$this->Excel->loadFromHTML($html);
		$this->Excel->create($filename, 'TINC_' . $report_name);
	}

	/**
	 * Takes a date string of format d-m-Y and transforms it into Y-m-d
	 * @param string $date The date to change
	 * @return string The transformed date
	 */
	function DateDisplayToObject($date) {
		$seperator = '-';
		if (strpos($date, '/') !== false)
			$seperator = '/';

		$dt_result = \DateTime::createFromFormat('d' . $seperator . 'm' . $seperator . 'Y', $date);

		return $dt_result;
	}
}
