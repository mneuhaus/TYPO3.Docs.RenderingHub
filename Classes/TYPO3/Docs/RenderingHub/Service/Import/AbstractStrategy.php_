<?php
namespace TYPO3\Docs\RenderingHub\Service\Import;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Ter data source
 *
 * @Flow\Scope("singleton")
 */
class AbstractStrategy implements \TYPO3\Docs\RenderingHub\Service\Import\StrategyInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Utility\RunTimeSettings
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
		$packages = $this->packageRepository->findByPackageKey($packageKey, $version);
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
		$counter = 0;
		$packages = $this->packageRepository->findAll();

		foreach ($packages as $package) {
			$document = $this->documentService->create($package);
			$this->documentService->build($document);

			if (++$counter >= $this->runTimeSettings->getLimit()) {
				break;
			}
		}
	}
}

?>