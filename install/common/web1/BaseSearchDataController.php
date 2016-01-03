<?php
namespace Trunk\Tinc;
require_once( CONTROLLERPATH . 'BaseController.php' );
$_ns = '\\Trunk\\Tinc';

class BaseSearchDataController extends BaseController {

    protected $default_sort = "";

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
        $page_no = $this->input->post( 'PageNumber' );
        $sort_order = $this->input->post( 'SortOrder' );
        $sort_direction = $this->input->post( 'SortDirection' );
        $excel_export = $this->input->post('ExcelExport') == 1;

        // Run the actual search
        $query = $this->_search( false, $parent_id, $parent_type );

        $sort_order = $sort_order == "" ? $this->default_sort : $sort_order;
        $sort_direction = $sort_direction == "" ? "asc" : $sort_direction;
        if ( strpos( $sort_order, "," ) !== false ) {
            $parts = explode( ",", $sort_order );
            foreach( $parts as $part ) {
                $query->orderBy( $part, $sort_direction == "desc" ? Criteria::DESC : Criteria::ASC );
            }
        }
        else {
            $query->orderBy( $sort_order, $sort_direction == "desc" ? Criteria::DESC : Criteria::ASC );
        }

        // If this isn't an excel export, and a page number has been requested
        if ( !$excel_export && $page_no > 0 ) {
            // Paginate the results
            $rows = $query->paginate( $page_no, 50 );

            $this->data[ 'pagination' ][ 'active_page' ] = $page_no;
            $this->data[ 'pagination' ][ 'needed' ] = $rows->haveToPaginate();
            $this->data[ 'pagination' ][ 'pages' ] = $rows->getLinks( 5 );
            $this->data[ 'pagination' ][ 'last_page' ] = $rows->getLastPage();
            $this->data[ 'pagination' ][ 'num_results' ] = $rows->getNbResults();
            $this->data[ 'pagination' ][ 'first_result' ] = $rows->getFirstIndex();
            $this->data[ 'pagination' ][ 'last_result' ] = $rows->getLastIndex();

        }
        else {
            $rows = $query->find();
        }

        $this->data[ 'table_rows' ] = $rows;
        $this->data[ 'count' ] = count( $rows );
        $this->data[ 'sort_order' ] = $sort_order;
        $this->data[ 'sort_dir' ] = $sort_direction;

        // If this is an excel export
        if ( $excel_export ) {
            $this->data['ExcelExport'] = true;
            // Output to excel
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
    /**
     * Actually perform the search and return an array of objects
     * @param bool $auto_complete Whether this is a auto-complete search and therefore should use different terms
     * @param null|object $parent_id Parent of this object (if relevant)
     * @param null|object $parent_type Type of the parent of this object (if relevant)
     * @return PropelQuery
     */
    protected function _search( $auto_complete = false, $parent_id = null, $parent_type = null ) {

    }

    /**
     * Save function - called to save the object given
     */
    public function save() {

    }

}