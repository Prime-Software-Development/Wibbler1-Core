<?php
namespace Trunk\Tinc;
require_once COMMONPATH . 'web_common/CoreController.php';
$_ns = '\\Trunk\\Tinc';

class BaseController extends \Trunk\Tinc\CoreController {

	/**
	 * Constructor
	 */
	function __construct( $dependencies = null ) {

		parent::__construct( $dependencies );

		if ( isset( $_SESSION[ 'user_id' ] ) ) {
			$this->current_user = \Database\UserQuery::create()->findPk($_SESSION['user_id']);
		}

	}
}
