<?php

namespace TYPO3\Docs\Aspect;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @FLOW3\Aspect
 */
class DoctrineCommandAspect {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Persistence\Doctrine\Mapping\Driver\Flow3AnnotationDriver
	 */
	protected $flow3AnnotationDriver;

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * Further object initialization
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->connection = $this->entityManager->getConnection();
		$this->tableName = $this->flow3AnnotationDriver->inferTableNameFromClassName('TYPO3\Docs\Domain\Model\Package');
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
	 */
	public function injectEntityManager(\Doctrine\Common\Persistence\ObjectManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * Add "enum" as additional mapping type for doctrine
	 *
	 * @param \TYPO3\FLOW3\Aop\JoinPointInterface $joinPoint
	 * @FLOW3\Before("method(TYPO3\FLOW3\Command\DoctrineCommandController->updateCommand())")
	 * @return boolean
	 */
	public function addCustomType(\TYPO3\FLOW3\Aop\JoinPointInterface $joinPoint) {
		$platform = $this->entityManager->getConnection()->getDatabasePlatform();
		$platform->registerDoctrineTypeMapping('enum', 'string');
	}

	/**
	 * Create a custom table for storing packages in a "personal" way. Actually, this table is meant for sto
	 *
	 * @param \TYPO3\FLOW3\Aop\JoinPointInterface $joinPoint
	 * @FLOW3\Before("method(TYPO3\FLOW3\Command\DoctrineCommandController->updateCommand())")
	 * @return boolean
	 */
	public function createTable(\TYPO3\FLOW3\Aop\JoinPointInterface $joinPoint) {

		/** @var $schemaManager \Doctrine\DBAL\Schema\AbstractSchemaManager */
		$schemaManager = $this->connection->getSchemaManager();

		if (!$schemaManager->tablesExist($this->tableName)) {

			$schema = new \Doctrine\DBAL\Schema\Schema();
			$table = $schema->createTable($this->tableName);

			// @todo try reading table definition from class meta-data
			#$i = $this->entityManager->getClassMetadata('TYPO3\Docs\Domain\Model\Package');
			$table->addColumn("title", "string", array("length" => 150));
			$table->addColumn("abstract", "text");
			$table->addColumn("product", "string", array("length" => 30));
			$table->addColumn("locale", "string", array("length" => 30));
			$table->addColumn("type", "string", array("length" => 30));
			$table->addColumn("packagekey", "string", array("length" => 100));
			$table->addColumn("uri", "string", array("length" => 255));
			$table->addColumn("repository", "string", array("length" => 255));
			$table->addColumn("version", "string", array("length" => 100));
			$table->addColumn("repositorytype", "string", array("length" => 30));
			$table->addColumn("repositorytag", "string", array("length" => 100));

			$platform = $this->connection->getDatabasePlatform();
			$queries = $schema->toSql($platform);

			foreach ($queries as $query) {
				$this->connection->exec($query);
			}
		}
	}
}

?>
