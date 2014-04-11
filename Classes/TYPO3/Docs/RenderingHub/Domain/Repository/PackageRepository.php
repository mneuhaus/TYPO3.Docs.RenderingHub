<?php

namespace TYPO3\Docs\RenderingHub\Domain\Repository;

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
class PackageRepository {

	const ENTITY_CLASSNAME = 'TYPO3\Docs\RenderingHub\Domain\Model\Package';

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\Ter\PackageRepository
	 */
	protected $terPackageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\Git\PackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
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
	 * Further object initialization
	 *
	 * @return void
	 */
	public function initializeObject() {
		$this->connection = $this->entityManager->getConnection();
		$this->tableName = $this->flowAnnotationDriver->inferTableNameFromClassName('TYPO3\Docs\RenderingHub\Domain\Model\Package');
	}

	/**
	 * Count the number of documents that would be processed
	 *
	 * @param string $repositoryType a repository type which can be git, ter, ...
	 * @return integer
	 * @throws \InvalidArgumentException
	 */
	public function countPackageToProcess($repositoryType) {
		switch ($repositoryType) {
			case 'ter':
				$numberOfPackagesWithVersions = $this->terPackageRepository->countAll();
				break;
			case 'git':
				$numberOfPackagesWithVersions = $this->gitPackageRepository->countAll();
				break;
			default:
				throw new \InvalidArgumentException('Invalid repository type "' . $repositoryType . '" given.', 1370522452);
		}
		$numberOfDocuments = $this->documentRepository->countByRepositoryType($repositoryType);

		return $numberOfPackagesWithVersions - $numberOfDocuments;
	}

	/**
	 * Persist data
	 *
	 * @param string $repositoryType
	 * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package[]
	 */
	public function findByRepositoryType($repositoryType) {
		return $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE repositoryType = ?", array($repositoryType));
	}

	/**
	 * Find TER packages by package (extension) key and version
	 *
	 * @param string $packageKey
	 * @param string $version (optional)
	 * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package[]
	 */
	public function findTerPackagesByPackageKey($packageKey, $version = NULL) {
		$packages = array();
		if (empty($version)) {
			$result = $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE repositoryType = ? AND packageKey = ?", array('ter', $packageKey));
		} else {
			$result = $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE repositoryType = ? AND packageKey = ? AND version = ?", array('ter', $packageKey, $version));
		}
		foreach ($result as $row) {
			$packages[] = new \TYPO3\Docs\RenderingHub\Domain\Model\Package($row);
		}
		return $packages;
	}

	/**
	 * Find git packages by package (extension) key and version
	 *
	 * @param string $packageKey
	 * @param string $version (optional)
	 * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package[]
	 */
	public function findGitPackagesByPackageKey($packageKey, $version = NULL) {
		$packages = array();
		if (empty($version)) {
			$result = $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE repositoryType = ? AND packageKey = ?", array('git', $packageKey));
		} else {
			$result = $this->connection->fetchAll("SELECT * FROM {$this->tableName} WHERE repositoryType = ? AND packageKey = ? AND version = ?", array('git', $packageKey, $version));
		}
		foreach ($result as $row) {
			$packages[] = new \TYPO3\Docs\RenderingHub\Domain\Model\Package($row);
		}
		return $packages;
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
