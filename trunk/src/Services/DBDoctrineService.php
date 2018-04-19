<?php
namespace Trunk\Wibbler\Services;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DBDoctrineService {

	/**
	 * @var EntityManager
	 */
	private $entity_manager;

	public function __construct( $additional_config = null ) {

		$dbParams = [
			'driver' => 'pdo_mysql',
			'user' => $additional_config[ 'user' ],
			'password' => $additional_config[ 'password' ],
			'dbname' => $additional_config[ 'dbname' ],
			'host' => $additional_config[ 'host' ],
		];

		// Setup the annotation reader
		$config = Setup::createAnnotationMetadataConfiguration([$additional_config[ 'path_to_entities' ]], ENVIRONMENT != "production", null, null, false );
		// Initiate a cache (in memory)
		$config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());

		// Create the entity manager
		$this->entity_manager = EntityManager::create( $dbParams, $config );

		// Register the enum type as a string
		$platform = $this->entity_manager->getConnection()->getDatabasePlatform();
		$platform->registerDoctrineTypeMapping('enum', 'string');
	}

	public function getEntityManager() {
		return $this->entity_manager;
	}
}