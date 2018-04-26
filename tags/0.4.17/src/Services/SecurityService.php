<?php
namespace Trunk\Wibbler\Services;

class SecurityService extends base {

	private $current_user;

	public function __construct() {
		parent::__construct();

		$this->current_user = $this->_find_current_user();
	}

	/**
	 * Gets the current user
	 */
	public function getCurrentUser() {
		return $this->current_user;
	}

	/**
	 * Find the current user (if possible)
	 */
	protected function _find_current_user() {
	}
}