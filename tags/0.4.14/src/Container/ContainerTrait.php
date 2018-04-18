<?php
namespace Trunk\Wibbler\Container;
use Trunk\Wibbler\WibblerDependencyContainer;

/**
 * Created by PhpStorm.
 * User: trunk
 * Date: 16/05/17
 * Time: 13:27
 */

trait ContainerTrait {
	/**
	 * @return WibblerDependencyContainer
	 */
	public function getContainer()
	{
		return WibblerDependencyContainer::Instance();
	}
}