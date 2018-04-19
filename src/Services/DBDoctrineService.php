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
		$config = Setup::createAnnotationMetadataConfiguration([$additional_config[ 'path_to_entities' ]], ENVIRONMENT != "production" );
		$this->entity_manager = EntityManager::create( $dbParams, $config );

		/*use Doctrine\Common\Annotations\AnnotationReader;
		use Doctrine\Common\Annotations\AnnotationRegistry;

			AnnotationRegistry::registerFile("/path/to/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
			AnnotationRegistry::registerAutoloadNamespace("Symfony\Component\Validator\Constraint", "/path/to/symfony/src");
			AnnotationRegistry::registerAutoloadNamespace("MyProject\Annotations", "/path/to/myproject/src");

			$reader = new AnnotationReader();
			AnnotationReader::addGlobalIgnoredName('dummy');*/
	}

	public function getEntityManager() {
		return $this->entity_manager;
	}
}