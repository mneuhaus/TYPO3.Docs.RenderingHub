<?php
namespace TYPO3\Docs\RenderingHub\Service\Document;

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
class GitService implements \TYPO3\Docs\RenderingHub\Service\Document\ServiceInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Service\Build\JobService
	 */
	protected $buildService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Uri
	 */
	protected $uriFinder;

	/**
	 * Instantiate a new Document and add it into the Repository.
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return \TYPO3\Docs\RenderingHub\Domain\Model\Document
	 */
	public function create(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {
		if ($this->documentRepository->notExists($package->getUri())) {
			$document = new \TYPO3\Docs\RenderingHub\Domain\Model\Document();
			$document->setTitle($package->getTitle());
			$document->setAbstract($package->getAbstract());
			$document->setStatus(\TYPO3\Docs\RenderingHub\Domain\Model\Document::STATUS_RENDER);
			$document->setType($package->getType());
			$document->setGenerationDate(new \DateTime('now'));
			$document->setVersion($package->getVersion());
			$document->setLocale($package->getLocale());
			$document->setProduct($package->getProduct());
			$document->setRepository($package->getRepository());
			$document->setRepositoryTag($package->getRepositoryTag());
			$document->setPackageKey($package->getPackageKey());
			$document->setUri($package->getUri());
			$document->setRepositoryType($package->getRepositoryType());

			$this->documentRepository->add($document);
			$this->systemLogger->log('Git: added new document object for ' . $package->getUri(), LOG_INFO);
		} else {
			$document = $this->documentRepository->findOneByUri($package->getUri());
			$this->documentRepository->update($document);
			$this->systemLogger->log('Git: updated document object ' . $package->getUri(), LOG_INFO);
		}

		return $document;
	}

	/**
	 * Render a Document given as input.
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
	 * @return void
	 */
	public function build(\TYPO3\Docs\RenderingHub\Domain\Model\Document $document) {
		$job = $this->buildService->create($document);
		$this->buildService->queue($job);
		$this->systemLogger->log('Git: added new job for document ' . $document->getUri(), LOG_INFO);
	}
}

?>