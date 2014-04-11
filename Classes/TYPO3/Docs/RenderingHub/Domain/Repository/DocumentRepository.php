<?php
namespace TYPO3\Docs\RenderingHub\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Docs\RenderingHub\Domain\Model\Document;
use TYPO3\Flow\Annotations as Flow;

/**
 * A repository for Documentations
 *
 * @Flow\Scope("singleton")
 */
class DocumentRepository extends \TYPO3\Flow\Persistence\Repository {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Uri
	 */
	protected $uriFinder;

	/**
	 * A reference to the Object Manager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Service\Build\JobService
	 */
	protected $buildService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Directory
	 */
	protected $directoryService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * Finds documents belonging to the Ter given a status
	 *
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The Ter documents
	 */
	public function findForHomePage() {
		$query = $this->createQuery();

		return $query->matching($query->logicalNot($query->equals('status', Document::STATUS_NOT_FOUND)))
			->setOrderings(array('repositoryType' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING))
			->execute();
	}

	/**
	 * Find documents to be sync.
	 * This corresponds to documents having the status "waiting-sync"
	 *
	 * @param string $packageKey
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface
	 */
	public function findDocumentsWaitingToBeSynced($packageKey) {
		$query = $this->createQuery();

		return $query->matching(
			$query->logicalAnd(
				$query->equals('packageKey', $packageKey),
				$query->equals('status', Document::STATUS_SYNC)
			)
		)->execute();
	}

	/**
	 * Finds documents belonging to the Ter given a status
	 *
	 * @param string $status the status of the document
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The Ter documents
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
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The Ter documents
	 */
	public function findGitDocumentsByStatus($status) {
		$query = $this->createQuery();
		return $query->matching(
			$query->logicalAnd(
				$query->equals('status', $status),
				$query->equals('repositoryType', 'git')
			)
		)->execute();
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
		return $document instanceof Document;
	}

	/**
	 * Tell whether a document does not exists
	 *
	 * @param string $uri the uri of a document
	 * @return boolean
	 */
	public function notExists($uri) {
		return !$this->exists($uri);
	}

	/**
	 * Update the Uri Alias for a package key.
	 *
	 * @param string $packageKey
	 */
	public function resetUriAlias($packageKey) {
		$documents = $this->findByPackageKey($packageKey);
		$documentWithHighestVersion = '';

		/** @var $document Document */
		foreach ($documents as $document) {

			if (!$documentWithHighestVersion) {
				$documentWithHighestVersion = $document;
			}

			if (version_compare($document->getVersion(), $documentWithHighestVersion->getVersion(), '>')) {
				$documentWithHighestVersion = $document;
			}

			// Default value should be empty
			if ($document->getUriAlias() !== '') {
				$document->setUriAlias('');
				$document->setStatus(Document::STATUS_SYNC);
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
	public function importByRepositoryType($repositoryType, $packageKey, $version) {
		$strategyClassName = 'TYPO3\Docs\RenderingHub\Service\Import\\' . ucfirst($repositoryType) . 'Strategy';
		$this->objectManager->get($strategyClassName)->import($packageKey, $version);
	}

	/**
	 * Import document coming from different packages repository type
	 *
	 * @param string $repositoryType
	 * @return void
	 */
	public function importAllByRepositoryType($repositoryType) {
		$strategyClassName = 'TYPO3\Docs\RenderingHub\Service\Import\\' . ucfirst($repositoryType) . 'Strategy';
		$this->objectManager->get($strategyClassName)->importAll();
	}

	/**
	 * Update the rendering of a all Documents.
	 *
	 * @return void
	 */
	public function updateAll() {
		$documents = $this->findAll();

		/** @var $document Document */
		foreach ($documents as $document) {

			$document->setStatus(Document::STATUS_RENDER);
			$this->update($document);
			$message = sprintf('%s: updating new document object %s', ucfirst($document->getRepositoryType()), $document->getUri());
			$this->systemLogger->log($message, LOG_INFO);

			$directory = $this->directoryService->getBuild($document);
			\TYPO3\Flow\Utility\Files::removeDirectoryRecursively($directory);

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