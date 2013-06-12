<?php
namespace TYPO3\Docs\Service\Import;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Git data source
 *
 * @Flow\Scope("singleton")
 */
class GitStrategy implements \TYPO3\Docs\Service\Import\StrategyInterface {

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
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Service\Document\GitService
	 */
	protected $documentService;

	/**
	 * @Flow\Inject
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
			if ($this->documentRepository->notExists($package->getUri())) {
				$document = $this->documentService->create($package);
				$this->documentService->build($document);

				if ($counter++ >= $this->runTimeSettings->getLimit()) {
					break;
				}
			}
		}
	}
}

?>