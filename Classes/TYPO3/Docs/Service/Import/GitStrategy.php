<?php
namespace TYPO3\Docs\Service\Import;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class dealing with Ter data source
 *
 * @FLOW3\Scope("singleton")
 */
class GitStrategy implements \TYPO3\Docs\Service\Import\StrategyInterface {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\Git\PackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Controller\DocumentController
	 */
	protected $documentController;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\Uri
	 */
	protected $uriFinder;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Service\Document\GitService
	 */
	protected $documentService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * Retrieve a TYPO3 package given a package name and its possible versions and then render them.
	 *
	 * @param string $packageKey the package name
	 * @param string $version the package name
	 * @return void
	 */
	public function import($packageKey, $version = '') {

		$packages = $this->gitPackageRepository->findByPackageKey($packageKey, $version);
		foreach ($packages as $package) {
			$document = $this->documentService->create($package);
			$this->documentService->build($document);
		}
	}

	/**
	 * Retrieve all TYPO3 packages from a repository and render them.
	 *
	 * @return void
	 */
	public function importAll() {

		$packages = $this->gitPackageRepository->findAll();
		$counter = 0;

		foreach ($packages as $package) {

			$uri = $this->uriFinder->getUri($package);

			if ($this->documentRepository->notExists($uri)) {

				$document = $this->documentService->create($package);
				$this->documentService->build($document);

				// prevent the script to loop too many times to keep the resources safe
				$counter++;
				if ($counter >= $this->runTimeSettings->getLimit()) {
					break;
				}
			}
		}
	}
}

?>