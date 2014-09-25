<?php
namespace MyApp\User;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(__dir__ . '/../BaseSearchDataController.php');
$_ns = '\\MyApp\\User';

class Data extends \MyApp\BaseSearchDataController {

	/**
	 * Processes the objects array indirectly got from _search
	 * Should return an array ready to output as json and including label and Id keys for each item
	 */
	protected function _process_autocomplete($objects) {
		
	}

	/**
	 * Actually perform the search and return an array of objects
	 * @param bool $auto_complete Whether this is a auto-complete search and therefore should use different terms
	 */
	protected function _search($auto_complete = false) {

		$result = array(
			array('id' => 1, 'name' => 'Admin', 'username' => 'admin@test.com'),
			array('id' => 2, 'name' => 'User', 'username' => 'user@test.com')
		);

		$this->data = array('count' => count($result), 'table_rows' => $result);
	}

	/**
	 * Save function - called to save the object given
	 */
	public function save() {
		
	}
}