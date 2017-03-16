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
	 * @param      $module
	 * @param bool $exception_on_fail Whether to emit an exception (true) or return an empty array (false) if the config can't be found
	 * @return bool
	 * @throws \Exception
	 */
	public function load( $module, $exception_on_fail = true ) {

		$file = COMMONPATH . 'config/' . $module . '.php';

		if ( !file_exists( $file ) ) {
			if ( $exception_on_fail ) {
				throw new \Exception( 'Config file not found' );
			}
			else {
				return [];
			}
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