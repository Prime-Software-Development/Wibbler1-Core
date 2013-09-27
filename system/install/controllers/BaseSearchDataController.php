<?php
namespace MyApp;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(__dir__ . '/BaseController.php');

class BaseSearchDataController extends BaseController {
	
	/**
	 * Autocomplete function - allows for searching using a single key field
	 */
	public function autocomplete(){

		// Find the objects using the search method
		$objects = $this->_search(true);

		if (count($this->data) == 0) {
			$results = array();
		}
		else {
			// Process the objects as required
			$results = $this->_process_autocomplete($this->data);
		}

		// Output the results as a json string
		$this->ShowJSON($results);
	}

	/**
	 * Processes the objects array indirectly got from _search
	 * Should return an array ready to output as json and including label and Id keys for each item
	 */
	protected function _process_autocomplete($objects) {
		
	}

	/**
	 * Search function - allows for the search to run using all required filters - outputs HTML
	 */
	public function search() {
		// Run the actual search
		$this->_search(false);

		// Output to twig
		$this->ShowTwig($this->controller_path . "search_results.twig");
	}

	/**
	 * Actually perform the search and return an array of objects
	 * @param bool $auto_complete Whether this is a auto-complete search and therefore should use different terms
	 */
	protected function _search($auto_complete = false) {

	}

	/**
	 * Save function - called to save the object given
	 */
	public function save() {
		
	}
	
}