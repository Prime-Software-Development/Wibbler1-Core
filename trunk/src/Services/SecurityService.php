<?php
namespace Trunk\Wibbler\Services;
use \Trunk\Wibbler\Modules\base;

class SecurityService extends base {

	private $current_user = false;

	/**
	 * Gets the current user
	 */
	public function getCurrentUser() {
		if ( $this->current_user === false )
			$this->current_user = $this->_find_current_user();

		return $this->current_user;
	}

	/**
	 * Find the current user (if possible)
	 */
	protected function _find_current_user() {
	}
}