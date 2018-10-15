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
	 * Whether we're being loaded via the command line
	 * @var bool
	 */
	protected $is_cli = false;

	public function __construct()
	{
		$this->is_cli = php_sapi_name() == "cli";
		parent::__construct();
	}

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