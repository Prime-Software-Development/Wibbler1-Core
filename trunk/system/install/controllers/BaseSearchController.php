<?php
namespace MyApp;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(__dir__ . '/BaseController.php');

class BaseSearchController extends BaseController {

	/**
	 * Index function - shows the default search screen - should not be overridden
	 */
	public function index() {
echo $this->urls->request_uri;die();
		// Initialise the search options
		$this->_init_search();

		// Output to twig
		$this->ShowTwig("index");
	}

	/**
	 * Initialises features required for the search screen - can be overridden
	 * Extra data should be added to the $this->data array
	 */
	protected function _init_search() {
		
	}

	/**
	 * Manage function - shows the management screen to edit the given object - should not be overridden
	 * @param int|empty $id ID of the object to manage
	 */
	public function manage($id = null) {

		// Initialise the search options
		$this->_init_search();
		// Initialise the manage options
		$this->_init_manage($id);

		// Output to twig
		$this->ShowTwig("manage");
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