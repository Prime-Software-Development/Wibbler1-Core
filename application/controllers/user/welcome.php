<?php
namespace MyApp\User;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(__dir__ . '/../BaseSearchController.php');
$_ns = '\\MyApp\\User';

class Welcome extends \MyApp\BaseSearchController {

	var $end_controller_path = __dir__;

	/**
	 * Constructor
	 */
	function __construct() {

		parent::__construct();
	}

	/**
	 * Initialises features required for the search screen - can be overridden
	 * Extra data should be added to the $this->data array
	 */
	protected function _init_search() {
		
	}

	/**
	 * Initialises features required for the management screen - can be overridden
	 * Search options required for the search drop down are automatically added prior to this call
	 * Extra data should be added to the $this->data array
	 * @param int|empty $id ID of the object to manage
	 */
	protected function _init_manage($id = null) {

	}

}