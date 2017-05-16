<?php
namespace Trunk\Wibbler\Modules;

abstract class base {

	protected $dependencies;

	function __construct($additional_config = null) {
		$this->dependencies = \Trunk\Wibbler\WibblerDependencyContainer::Instance($additional_config);
	}

	static $instances = array();

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

}