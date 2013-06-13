<?php
namespace TYPO3\Docs\Service\Import;

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
class TerStrategy extends AbstractStrategy {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\Ter\PackageRepository
	 */
	protected $packageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Service\Document\TerService
	 */
	protected $documentService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Uri
	 */
	protected $uriFinder;

}

?>