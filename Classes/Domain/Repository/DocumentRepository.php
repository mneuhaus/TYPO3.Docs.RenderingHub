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
	 * @var \TYPO3\Docs\Finder\Uri
	 */
	protected $uriFinder;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Service\ImportService
	 */
	protected $importService;

	/**
	 * A reference to the Object Manager
	 *
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Service\Build\JobService
	 */
	protected $buildService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\Directory
	 */
	protected $directoryService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * Finds documents belonging to the Ter given a status
	 *
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The Ter documents
	 */
	public function findForHomePage() {
		$query = $this->createQuery();
//		return $query->matching(
//			$query->logicalAnd(
//				$query->like('repository', '/Documentation/TYPO3/%'),
//				$query->equals('repositoryType', 'git')
//			)
//		)
		$result = $query
			->setOrderings(array('repositoryType' => \TYPO3\FLOW3\Persistence\QueryInterface::ORDER_ASCENDING))
			->execute();

		return $result;
	}

	/**
	 * Finds documents belonging to the Ter given a status
	 *
	 * @param string $status the status of the document
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The Ter documents
	 */
	public function findTerDocumentsByStatus($status) {
		$query = $this->createQuery();
		return $query->matching(
			$query->logicalAnd(
				$query->equals('status', $status),
				$query->equals('repositoryType', 'ter')
			)
		)->execute();
	}

	/**
	 * Find documents to be sync.
	 * This corresponds to documents having the status "waiting-sync"
	 *
	 * @param string $packageKey
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface
	 */
	public function findDocumentToBeSync($packageKey) {
		$query = $this->createQuery();
		return $query->matching(
			$query->logicalAnd(
				$query->equals('packageKey', $packageKey),
				$query->equals('status', \TYPO3\Docs\Utility\StatusMessage::SYNC)
			)
		)->execute();
	}

	/**
	 * counts documents belonging to the Ter given a status
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
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The Ter documents
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
	public function exists($uri) {
		$document = $this->findOneByUri($uri);
		return $document instanceof \TYPO3\Docs\Domain\Model\Document;
	}

	/**
	 * Tell whether a document does not exists
	 *
	 * @param string $uri the uri of a document
	 * @return boolean
	 */
	public function NotExists($uri) {
		return ! $this->exists($uri);
	}

	/**
	 * Update the Uri Alias for a package key.
	 *
	 * @param string $packageKey
	 */
	public function resetUriAlias($packageKey) {

		$documents = $this->findByPackageKey($packageKey);

		$documentWithHighestVersion = '';

		/** @var $document \TYPO3\Docs\Domain\Model\Document */
		foreach ($documents as $document) {

			if (!$documentWithHighestVersion) {
				$documentWithHighestVersion = $document;
			}

			if (version_compare($document->getVersion(), $documentWithHighestVersion->getVersion(), '>')) {
				$documentWithHighestVersion = $document;
			}

			// Default value should be empty
			if ($document->getUriAlias() != '') {
				$document->setUriAlias('');
				$document->setStatus(\TYPO3\Docs\Utility\StatusMessage::SYNC);
				$this->update($document);
			}
		}

		$uri = $this->uriFinder->getUri($document->toPackage());

		// Remove last segment
		$parts = explode('/', $uri);
		array_pop($parts);
		$documentWithHighestVersion->setUriAlias(implode('/', $parts) . '/latest');
		$this->update($documentWithHighestVersion);
	}

	/**
	 * Import document coming from different packages repository type
	 *
	 * @param string $repositoryType
	 * @param string $packageKey the package name
	 * @param string $version the package name
	 * @return void
	 */
	public function importByRepositoryType($repositoryType, $package, $version) {
		/** @var $strategyInterface \TYPO3\Docs\Service\Import\StrategyInterface */
		$strategyInterface = 'Import\\' . ucfirst($repositoryType) . 'Strategy';
		$this->importService->setStrategy($strategyInterface)
			->import($package, $version);
	}

	/**
	 * Update the rendering of a all Documents.
	 *
	 * @return void
	 */
	public function updateAll() {
		$documents = $this->documentRepository->findAll();

		/** @var $document \TYPO3\Docs\Domain\Model\Document */
		foreach ($documents as $document) {

			$document->setStatus(\TYPO3\Docs\Utility\StatusMessage::RENDER);
			$this->documentRepository->update($document);
			$message = sprintf('%s: added new document object %s', ucfirst($document->getRepositoryType()), $document->getUri());
			$this->systemLogger->log($message, LOG_INFO);

			$directory = $this->directoryService->getBuild($document);
			\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($directory);

			// Create a job and insert it into the queue
			$job = $this->buildService->create($document);
			$this->buildService->queue($job);

			if ($documents->key() >= $this->runTimeSettings->getLimit() - 1) {
				break;
			}
		}
	}
}
?>