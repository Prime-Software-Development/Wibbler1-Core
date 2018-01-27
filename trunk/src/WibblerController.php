<?php
namespace Trunk\Wibbler;

class WibblerController {
    private $_dependencies;

    /**
     * Holds the full URL path to this controller
     * @var
     */
    protected $controller_path;

    /**
     * Holds an array of the URL path to the controller
     * @var array
     */
    protected $controller_path_parts = [ ];

    /**
     * @var \Trunk\Wibbler\Modules\Config
     */
    protected $config;

    /**
     * @var
     */
    protected $request;

    /**
     * Initiate the controller - called after construction by the main Wibbler class
     */
    function __construct() {
        $this->_load_configs();
    }

    /**
     * Function called after the constructor, but before the main method
     * This function will know the method being called and it's doc block
     */
    public function pre_function_call( $method, $docblock = null ) {
    }

    /**
     * Load a user module - the file name and the class name must be identical
     * @param string $module Name of the module to load
     */
    public function load_module( $module, $namespace = null, $option = null ) {
        $this->$module = $this->_dependencies->getModule( $module, $namespace, $option );
    }

    /**
     * Load a user helper file
     * @param string $helper Name of the helper file to load
     */
    public function load_helper( $helper ) {
        $this->_dependencies->getHelper( $helper );
    }

    /**
     * Load a user service
     * @param $service_name
     * @return mixed
     */
    public function load_service( $service_name ) {
        return $this->_dependencies->getService( $service_name );
    }

    /**
     * Sets the path and parts of the path to the controller
     * @param $additional_config
     * @param $controller_path
     * @param $controller_path_parts
     */
    public function _set_controller_details( $controller_path, $controller_path_parts ) {
        $this->controller_path = $controller_path;
        $this->controller_path_parts = $controller_path_parts;
    }

    /**
     * Sets the request object
     * @param $request
     */
    public function set_request( $request ) {
        $this->request = $request;
    }
    /**
     * Uses the loaded config to autoload modules, helpers and services as required
     */
    private function _load_configs() {

        // Keep a note of the dependency manager
        $this->_dependencies = WibblerDependencyContainer::Instance();

        // Load the configuration loading module
        $this->load_module( "config" );

        // Get the autoload config
        $loaded_config = $this->config->getConfig( 'config' );

        // If there are modules to load
        if ( isset( $loaded_config[ 'autoload' ][ 'modules' ] ) ) {
            $this->___load_modules( $loaded_config[ 'autoload' ][ 'modules' ] );
        }
        // If there are helpers to load
        if ( isset( $loaded_config[ 'helpers' ] ) ) {
            $this->__load_helpers( $loaded_config[ 'helpers' ] );
        }

        // If there are modules to load
        if ( isset( $loaded_config[ 'autoload' ][ 'services' ] ) ) {
            $this->__load_services( $loaded_config[ 'autoload' ][ 'services' ] );
        }
    }

    /**
     * Load the required helpers
     * @param $helpers
     */
    private function __load_helpers( $helpers ) {
        // Go through the helpers to autoload
        foreach ( $helpers as $helper ) {
            // And load them
            $this->load_helper( $helper );
        }
    }

    /**
     * Load the required modules
     * @param $modules
     */
    private function ___load_modules( $modules ) {
        // Go through the modules to autoload
        foreach ( $modules as $module ) {
            // And load them (default namespace)
            $this->load_module( $module );
        }
    }

    /**
     * Load the required services
     * @param $services
     */
    private function __load_services( $services ) {
        foreach ( $services as $service_name ) {
            $this->$service_name = $this->_dependencies->getService( $service_name );
        }
    }
}