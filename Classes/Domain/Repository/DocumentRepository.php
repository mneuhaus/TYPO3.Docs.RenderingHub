<?php
namespace TYPO3\Docs\Domain\Repository;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A repository for Documentations
 *
 * @FLOW3\Scope("singleton")
 */
class DocumentRepository extends \TYPO3\FLOW3\Persistence\Repository {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Build\Domain\Repository\TerPackageRepository
	 */
	protected $terPackageRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Build\Domain\Repository\GitPackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * Finds documents belonging to the TER given a status
	 *
	 * @param string $status the status of the document
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The TER documents
	 */
	public function findTerDocumentsByStatus($status) {
		$query = $this->createQuery();
		return $query->matching(
			$query->logicalAnd(
				$query->equals('status', $status),
				$query->equals('repositoryType', 'ter')
			)
		)
			->execute();
	}

	/**
	 * counts documents belonging to the TER given a status
	 *
	 * @param string $status the status of the document
	 * @return integer
	 */
	public function countTerDocumentsByStatus($status) {
		return $this->findTerDocumentsByStatus($status)->count();
	}

	/**
	 * Finds documents belonging to git.typo3.org given a status
	 *
	 * @param string $status the status of the document
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The TER documents
	 */
	public function findGitDocumentsByStatus($status) {
		$query = $this->createQuery();
		return $query->matching(
			$query->logicalAnd(
				$query->equals('status', $status),
				$query->equals('repositoryType', 'git')
			)
		)
			->execute();
	}

	/**
	 * counts documents belonging to git.typo3.org given a status
	 *
	 * @param string $status the status of the document
	 * @return integer
	 */
	public function countGitDocumentsByStatus($status) {
		return $this->findGitDocumentsByStatus($status)->count();
	}

	/**
	 * Tell whether a document exists given a URI
	 *
	 * @param string $uri the uri of a document
	 * @return boolean
	 */
	public function exist($uri) {
		$document = $this->findOneByUri($uri);
		return $document instanceof \TYPO3\Docs\Domain\Model\Document;
	}

	/**
	 * Count the number of documents that would be processed
	 *
	 * @param string $repositoryType a repository type which can be git, ter, ...
	 * @return int
	 */
	public function countDocumentToProcess($repositoryType) {
		$packageRepository = sprintf('%sPackageRepository', $repositoryType);
		$numberOfPackagesWithVersions = $this->$packageRepository->countAllWithVersions();
		$numberOfDocuments = $this->countByRepositoryType($repositoryType);

		return $numberOfPackagesWithVersions - $numberOfDocuments;
	}

}
?>