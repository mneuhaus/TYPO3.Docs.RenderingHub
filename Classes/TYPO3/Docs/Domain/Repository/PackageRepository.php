<?php

namespace TYPO3\Docs\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A repository for Git packages
 *
 * @Flow\Scope("singleton")
 */
class PackageRepository extends \TYPO3\Docs\Domain\Repository\AbstractRepository {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\Ter\PackageRepository
	 */
	protected $terPackageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\Git\PackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\Doctrine\Mapping\Driver\FlowAnnotationDriver
	 */
	protected $flowAnnotationDriver;

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
	 */
	public function injectEntityManager(\Doctrine\Common\Persistence\ObjectManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * Further object initialization
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->connection = $this->entityManager->getConnection();
		$this->tableName = $this->flowAnnotationDriver->inferTableNameFromClassName('TYPO3\Docs\Domain\Model\Package');
	}

	/**
	 * Count the number of documents that would be processed
	 *
	 * @param string $repositoryType a repository type which can be git, ter, ...
	 * @return int
	 */
	public function countPackageToProcess($repositoryType) {
		$packageRepository = sprintf('%sPackageRepository', $repositoryType);
		$numberOfPackagesWithVersions = $this->$packageRepository->countAll();
		$numberOfDocuments = $this->documentRepository->countByRepositoryType($repositoryType);

		return $numberOfPackagesWithVersions - $numberOfDocuments;
	}

	/**
	 * Persist data
	 *
	 * @param string $repositoryType
	 * @return \TYPO3\Docs\Domain\Model\Package[]
	 */
	public function findByRepositoryType($repositoryType) {
		return $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE repositoryType = ?", array($repositoryType));
	}

	/**
	 * Add a new package
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function add($data) {
		$this->connection->insert($this->tableName, (array)$data);
	}

	/**
	 * Delete records given a repository type
	 *
	 * @param string $repositoryType
	 */
	public function deleteByRepositoryType($repositoryType) {
		$data['repositoryType'] = $repositoryType;
		$this->connection->delete($this->tableName, (array)$data);
	}
}

?>
