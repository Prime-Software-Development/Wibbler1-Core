<?php
namespace Trunk\Tinc;
require_once(CONTROLLERPATH . 'BaseController.php');
$_ns = '\\Trunk\\Tinc';

class BaseSearchController extends BaseController {

    var $end_controller_path = '';
    var $path_parts = null;
    var $twig_item_name = "object";
    var $breadcrumb_name = "Object";

    /**
     * Index function
     * Shows the default search screen as the root window - should not be overridden
     * @param int|empty $id Id of the parent object to search for
     */
    public function index( $id = null ) {
        $this->data[ 'subpage' ] = false;
        $this->_all_index( $id );
    }

    /**
     * Index function
     * Shows the default search screen as a sub-window - should not be overridden
     * @param int|empty $id Id of the parent object to search for
     */
    public function subindex( $id = null ) {
        $this->data[ 'subpage' ] = true;
        $this->_all_index( $id );
    }

    /**
     * Function which actually outputs the relevant twig for the index functions
     * @param int|empty $id
     */
    private function _all_index( $id = null ) {

        // Note the breadcrumb to be displayed
        $this->data[ 'breadcrumb' ] = $this->breadcrumb_name;

        // Note this isn't the manager screen for twig
        $this->data['manage_page'] = false;

        // Initialise the search options
        $this->_init_search( $id );

        // Output to twig
        $this->ShowTwig($this->controller_path . "index.twig");

    }


    /**
     * Create a new item of this type
     * @param int|empty $parent_id Id of the parent object
     * @param string|empty $path Comma seperated path to the parent items of this item
     */
    public function create( $parent_id = null, $path = "" ) {
        $this->data[ 'subpage' ] = true;
        $this->_my_manage( $path, null, $parent_id );
    }

    /**
     * Manage function
     * Shows the management screen to edit the given object as the root window - should not be overridden
     * @param int|empty $id ID of the object to manage
     * @param int|empty $parent_id Id of the parent object
     * @param string|empty $path Comma seperated path to the parent items of this item
     */
    public function manage( $id = null, $path = null, $parent_id = null ) {
        $this->data[ 'subpage' ] = false;
        $this->_my_manage( $path, $id, $parent_id );
    }

    /**
     * Manage function
     * Shows the management screen to edit the given object as a sub-window - should not be overridden
     * @param int|empty $id ID of the object to manage
     * @param int|empty $parent_id Id of the parent object
     * @param string|empty $path Comma seperated path to the parent items of this item
     */
    public function submanage( $id = null, $path = null, $parent_id = null ) {
        $this->data[ 'subpage' ] = true;
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

        // Get the managed item
        $managed_item = $this->_get_managed_item( $id, $parent_id, $parent_object_type );

        $this->data[ 'ParentObjectId' ] = isset( $managed_item->parent_id ) ? $managed_item->parent_id : null;
        $this->data[ 'ParentObjectType' ] = isset( $managed_item->parent_type ) ? $managed_item->parent_type : $parent_object_type;
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
        $this->data['manage_page'] = true;

        // Output to twig
        $this->ShowTwig($this->controller_path . "index.twig");

    }

    /**
     * Gets the managed item or creates a new one with the relevant parent details assigned
     * @param int|empty $id
     * @param int|empty $parent_id
     * @param string|empty $parent_type
     */
    protected function _get_managed_item( $id = null, $parent_id = null, $parent_type = null ) {

    }

    /**
     * Initialises features required for the search screen - can be overridden
     * Extra data should be added to the $this->data array
     */
    protected function _init_search( $id = null ) {

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