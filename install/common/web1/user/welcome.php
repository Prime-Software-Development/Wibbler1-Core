<?php
namespace MyApp\User;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(__dir__ . '/../BaseSearchController.php');
$_ns = '\\MyApp\\User';

class Welcome extends \MyApp\BaseSearchController {

	var $end_controller_path = __dir__;
	var $twig_item_name = "user";
	var $breadcrumb_name = "User";

	/**
	 * @param int $id
	 * @param int|null $parent_id
	 * @param string|null $parent_type
	 * @return mixed
	 */
	protected function _get_managed_item( $id = null, $parent_id = null, $parent_type = null ) {
		return \Database\UserQuery::create()->findPk( $id );
	}

	/**
	 * Initialises features required for the management screen - can be overridden
	 * Search options required for the search drop down are automatically added prior to this call
	 * Extra data should be added to the $this->data array
	 * @param PropelObject|empty $managed_item Item being managed
	 */
	protected function _init_manage( $managed_item = null ) {
		$this->data[ 'breadcrumb' ] = $managed_item ? ($managed_item->getName()) : "New User";
	}

}