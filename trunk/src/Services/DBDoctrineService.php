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
		];
		$config = Setup::createAnnotationMetadataConfiguration([$additional_config[ 'path_to_entities' ]], ENVIRONMENT != "production" );
		$this->entity_manager = EntityManager::create( $dbParams, $config );
	}

	public function getEntityManager() {
		return $this->entity_manager;
	}
}