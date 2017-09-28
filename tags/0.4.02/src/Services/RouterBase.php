<?php
namespace Trunk\Wibbler\Services;

abstract class RouterBase {

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

	public function getController() {
		return $this->controller;
	}

	public function getError() {
		return $this->error;
	}
}