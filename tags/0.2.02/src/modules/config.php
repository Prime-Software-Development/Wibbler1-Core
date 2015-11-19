<?php
namespace Trunk\Wibbler\Modules;

class config extends base {

	/**
	 * All of the config parameters loaded so far
	 * @var array
	 */
	var $config_params = [];

	function __construct() {
	}

	/**
	 * Loads the config for the given module
	 * @param $module
	 * @return bool
	 * @throws \Exception
	 */
	public function load( $module ) {

		$file = COMMONPATH . 'config/' . $module . '.php';

		if ( !file_exists( $file ) ) {
			throw new \Exception( 'Config file not found' );
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