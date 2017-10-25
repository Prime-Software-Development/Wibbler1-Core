<?php
namespace Trunk\Wibbler\Services;

use Trunk\Wibbler\Modules\base;

abstract class RouterBase extends base {

	/**
	 * The controller which matches the given path
	 * @var WibblerController
	 */
	protected $controller = null;

	/**
	 * Any fatal error found when trying to load the class / method or false if all is ok
	 * @var mixed
	 */
	protected $error = false;

	public function handleRequest($request, $options = []) {

	}

	public function getError() {
		return $this->error;
	}
}