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
class GitStrategy extends AbstractStrategy {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\Git\PackageRepository
	 */
	protected $packageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Service\Document\GitService
	 */
	protected $documentService;

}

?>