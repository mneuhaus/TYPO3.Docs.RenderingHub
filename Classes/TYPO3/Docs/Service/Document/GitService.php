<?php
namespace TYPO3\Docs\Service\Document;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Git Document
 *
 * @Flow\Scope("singleton")
 */
class GitService implements \TYPO3\Docs\Service\Document\ServiceInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Service\Build\JobService
	 */
	protected $buildService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Uri
	 */
	protected $uriFinder;

	/**
	 * Instantiate a new Document and add it into the Repository.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return \TYPO3\Docs\Domain\Model\Document
	 */
	public function create(\TYPO3\Docs\Domain\Model\Package $package) {

		$uri = $this->uriFinder->getUri($package);

		$document = new \TYPO3\Docs\Domain\Model\Document();
		$document->setTitle($package->getTitle());
		$document->setAbstract($package->getAbstract());
		$document->setStatus(\TYPO3\Docs\Utility\StatusMessage::RENDER);
		$document->setType($package->getType());
		$document->setGenerationDate(new \DateTime('now'));
		$document->setVersion($package->getVersion());
		$document->setLocale($package->getLocale());
		$document->setProduct($package->getProduct());
		$document->setRepository($package->getRepository());
		$document->setRepositoryTag($package->getRepositoryTag());
		$document->setPackageKey($package->getPackageKey());
		$document->setUri($uri);
		$document->setRepositoryType($package->getRepositoryType());

		// Insert the document in the database
		$this->documentRepository->add($document);
		$this->systemLogger->log('Git: added new document object ' . $uri, LOG_INFO);

		return $document;
	}

	/**
	 * Render a Document given as input.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return void
	 */
	public function build(\TYPO3\Docs\Domain\Model\Document $document) {

		// Create a job and insert it into the queue
		$job = $this->buildService->create($document);
		$this->buildService->queue($job);
		$this->systemLogger->log('Git: added new job for document ' . $document->getUri(), LOG_INFO);
	}
}

?>