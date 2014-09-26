<?php
namespace TYPO3\Docs\RenderingHub\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Docs\RenderingHub\Service\ImportService;
use TYPO3\Docs\RenderingHub\Utility\Console;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Flow\Utility\Files;

/**
 * Document rendering command controller.
 *
 * @Flow\Scope("singleton")
 */
class ImportCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @Flow\Inject
	 * @var ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @return void
	 */
	public function allCommand() {
		$combo = new \TYPO3\Docs\RenderingHub\Domain\Model\Combo();
		$combo->setName('Import all from TER');

		$combo->createTask('\TYPO3\Docs\RenderingHub\Domain\Model\Task\Ter\FetchDatasourceTask');
		$combo->createTask('\TYPO3\Docs\RenderingHub\Domain\Model\Task\RemoveAllPackagesBySourceTask')->setDataSource('ter');
		$combo->createTask('\TYPO3\Docs\RenderingHub\Domain\Model\Task\Ter\CreatePackageCombosTask');
		$combo->queue();
		// $combo->execute();
	}

}

?>