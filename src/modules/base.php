<?php
namespace Trunk\Wibbler\Modules;

abstract class base {

	protected $dependencies;

	function __construct($additional_config = null) {
		$this->dependencies = \Trunk\Wibbler\WibblerDependencyContainer::Instance($additional_config);
	}

	static $instances = array();

	/**
	 * Get an instance of this class
	 * @param $options
	 * @return mixed
	 */
	final public static function Instance( $options )
	{
		//static $instances = array();

		$calledClass = get_called_class();

		if (!isset($instances[$calledClass]))
		{
			self::$instances[$calledClass] = new $calledClass( $options );
		}

		return self::$instances[$calledClass];
	}

	/**
	 * Load the service
	 * @param $service_name
	 * @return bool
	 */
	public function load_service( $service_name ) {
		return $this->dependencies->getService( $service_name );
	}

	/**
	 * Load the module and return it
	 * @param $module_name
	 * @param null $namespace
	 * @param null $options
	 * @return mixed
	 */
	public function load_module( $module_name, $namespace = null, $options = null ) {
		return $this->dependencies->getModule( $module_name, $namespace, $options );
	}
}