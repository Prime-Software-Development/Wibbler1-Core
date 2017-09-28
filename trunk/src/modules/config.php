<?php
namespace Trunk\Wibbler\Modules;

use Symfony\Component\Yaml\Yaml;

class config extends base {

	/**
	 * All of the config parameters loaded so far
	 * @var array
	 */
	var $config_params = [ "config" => [] ];

	function __construct() {
	}

	/**
	 * Loads the config for the given module
	 * @param      $module
	 * @param bool $exception_on_fail Whether to emit an exception (true) or return an empty array (false) if the config can't be found
	 * @return bool
	 * @throws \Exception
	 */
	public function load( $module, $exception_on_fail = true ) {

		$config = $this->_load_from_yaml($module );
		if ( $config === false ) {
			$config = $this->_load_from_php( $module );
			if ( $config === false ) {
				if ( $exception_on_fail ) {
					throw new \Exception( 'Config file not found' );
				}
			}
		}
		return $config;
	}

	/**
	 * Load the config from a yaml file
	 * @param $module
	 * @return bool|mixed
	 */
	private function _load_from_yaml( $module ) {
		$file = COMMONPATH . 'config/' . $module . '.yml';

		if ( !file_exists( $file ) ) {
			return false;
		}

		try {
			// Parse the config file
			$config = Yaml::parse( file_get_contents( $file ) );
		}
		catch(\Exception $e ) {
			printf("Unable to parse the YAML string: %s", $e->getMessage());
			die();
		}

		// Merge the new parameters into the config
		$this->config_params[ $module ] = $config;

		return $config;
	}

	/**
	 * Loads the config from a php file
	 * @param $module
	 * @return array|bool
	 */
	private function _load_from_php( $module ) {
		$file = COMMONPATH . 'config/' . $module . '.php';

		if ( !file_exists( $file ) ) {
			return false;
		}

		// Create a new emtpy config variable
		$config = [];

		// Include the file - should re-create the config variable
		include( $file );

		// Merge the new parameters into the config
		$this->config_params[ $module ] = $config;

		return $config;
	}

	/**
	 * @param array  $config_options
	 * @param string $array_key
	 */
	public function add_from_array( $config_options, $array_key = "config" ) {
		// If some config options have been passed in
		if ( $config_options !== null ) {
			// Merge the config arrays
			$this->config_params[ $array_key ] = array_merge_recursive( $this->config_params[ $array_key ], $config_options );
		}
	}

	public function getConfig( $module ) {
		if ( isset( $this->config_params[ $module ] ) ) {
			return $this->config_params[ $module ];
		}
	}
	/**
	 * Retrieves the requrested parameter from the loaded configs
	 * @param string $item_name
	 * @return mixed
	 */
	public function item( $item_name ) {

		$result = null;

		// Go through all of the modules loaded
		foreach( $this->config_params as $module => $values ) {

			// Go through each parameter within the module
			foreach( $values as $param => $value ) {

				// If the parameter matches what we're looking for
				if ( $param == $item_name ) {
					// Set the result
					$result = $value;
				}
			}
		}

		return $result;
	}

}