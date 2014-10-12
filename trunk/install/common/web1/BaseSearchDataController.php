<?php
namespace Trunk\Tinc;
require_once( CONTROLLERPATH . 'BaseController.php' );
$_ns = '\\Trunk\\Tinc';

class BaseSearchDataController extends BaseController {

    /**
     * Autocomplete function - allows for searching using a single key field
     */
    public function autocomplete( $parent_type = null, $parent_id = null ){

        // Find the objects using the search method
        $this->_search( true, $parent_id, $parent_type );

        if (count($this->data) == 0) {
            $results = array();
        }
        else {
            // Process the objects as required
            $results = $this->_process_autocomplete($this->data);
        }

        // Output the results as a json string
        $this->ShowJSON($results);
    }

    /**
     * Processes the objects array indirectly got from _search
     * Should return an array ready to output as json and including label and Id keys for each item
     */
    protected function _process_autocomplete( $objects ) {

        $result = array();

        foreach( $objects['table_rows'] as $row ) {
            $tmp_row = array();
            $tmp_row['label'] = $row->getName();
            $tmp_row['id'] = $row->getId();
            $result[] = $tmp_row;
        }

        return $result;
    }

    /**
     * Search function - allows for the search to run using all required filters - outputs HTML
     */
    public function search() {
        $parent_id = $this->input->post( 'ParentId' );
        $parent_type = $this->input->post( 'ParentType' );

        // Run the actual search
        $this->_search( false, $parent_id, $parent_type );

        if ( $this->input->post('ExcelExport') == 1 ) {
            $this->data['ExcelExport'] = true;
            // Output to excel
#			$this->ShowTwig($this->controller_path . "search_results.twig");
            $this->GenerateExcel($this->controller_path . "search_results.twig", "Fred");
        }
        else {
            // Output to twig
            $this->ShowTwig($this->controller_path . "search_results.twig");
        }
    }

    /**
     * Actually perform the search and return an array of objects
     * @param bool $auto_complete Whether this is a auto-complete search and therefore should use different terms
     */
    protected function _search( $auto_complete = false, $parent_id = null, $parent_type = null ) {

    }

    /**
     * Save function - called to save the object given
     */
    public function save() {

    }

}