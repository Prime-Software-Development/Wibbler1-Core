<?php
namespace Trunk\Tinc;
require_once(CONTROLLERPATH . 'BaseController.php');
$_ns = '\\Trunk\\Tinc';

class BaseSearchController extends BaseController {

    var $end_controller_path = '';
    var $path_parts = null;
    var $twig_item_name = "object";

    /**
     * Index function - shows the default search screen - should not be overridden
     */
    public function index( $id = null ) {

        // Initialise the search options
        $this->_init_search( $id );

        // Note this isn't the manager screen for twig
        $this->data['IsManager'] = false;

        // Output to twig
        $this->ShowTwig($this->controller_path . "index.twig");
    }

    /**
     * Create a new item of this type
     * @param type $parent_id
     */
    public function create( $parent_id = null, $path = "" ) {
        $this->_my_manage( $path, null, $parent_id );
    }

    /**
     * Manage function - shows the management screen to edit the given object - should not be overridden
     * @param int|empty $id ID of the object to manage
     */
    public function manage( $id = null, $path = null, $parent_id = null ) {
        $this->_my_manage( $path, $id, $parent_id );
    }

    /**
     * Actually setup the management page
     * @param type $path
     * @param type $id
     * @param type $parent_id
     */
    private function _my_manage( $path, $id, $parent_id = null ) {

        $this->path_parts = explode( ",", $path );
        // Put the cursor at the end of the array
        end( $this->path_parts );
        // Get the last but one item from the array (parent of the current object)
        $parent_object_type = prev( $this->path_parts );

        $managed_item = $this->_get_managed_item( $id, $parent_id, $parent_object_type );
        $this->data[ 'BreadcrumbList' ] = $managed_item->get_breadcrumb_part( $this->path_parts );
        $this->data[ 'ParentObjectId' ] = isset( $managed_item->parent_id ) ? $managed_item->parent_id : null;
        $this->data[ 'ParentObjectType' ] = $parent_object_type;
        $this->data[ 'IsNewObject' ] = $id == null || $id == "0";

        $this->data[ $this->twig_item_name ] = $managed_item;

        // Initialise the search options
        $this->_init_search();
        // Initialise the manage options
        $this->_init_manage( $managed_item );

        // If a parent has been defined we are creating a new object
        if ($parent_id != null) {
            $this->_init_create( $managed_item );
        }

        // Note this is the manager screen for twig
        $this->data['IsManager'] = true;
        $this->data['BreadcrumbPath'] = $path;

        // Output to twig
        $this->ShowTwig($this->controller_path . "index.twig");

    }

    protected function _get_managed_item( $id = null, $parent_id = null, $parent_type = null ) {

    }

    /**
     * Initialises features required for the search screen - can be overridden
     * Extra data should be added to the $this->data array
     */
    protected function _init_search() {

    }

    /**
     * Initialises features required for the management screen - can be overridden
     * Search options required for the search drop down are automatically added prior to this call
     * Extra data should be added to the $this->data array
     * @param int|empty $id ID of the object to manage
     */
    protected function _init_manage( $managed_item = null ) {

    }

    /**
     * Initialises the features required for creating a new object
     * @param type $parent_id
     */
    protected function _init_create( $parent_id = null ) {

    }
}