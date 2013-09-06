<?php
namespace MyApp;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$_ns = '\\MyApp';

class BaseController extends \Wibbler\WibblerController {

	/**
	 * Generates the html to either render or display
	 * @param type $template
	 * @param type $data
	 * @return type
	 */
	private function _GenerateTwig($template, $data) {
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