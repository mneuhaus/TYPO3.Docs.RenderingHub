<?php
namespace TYPO3\Docs\Service;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class providing service related to document importation
 *
 */
class ImportService implements \TYPO3\Docs\Service\Import\StrategyInterface {

	/**
	 * @var \TYPO3\Docs\Service\Import\StrategyInterface
	 */
	protected $strategy;

	/**
	 * Import all documents given a known strategy
	 *
	 * @param string $packageKey the package name
	 * @param string $version the package name
	 * @throws \TYPO3\Docs\Exception\MissingStrategyException
	 */
	public function import($packageKey, $version = '') {
		if (empty($this->strategy)) {
			throw new \TYPO3\Docs\Exception\MissingStrategyException('Missing Import Strategy', 1354627939);
		}
		$this->strategy->import($packageKey, $version = '');
	}

	/**
	 * Import all documents given a known strategy
	 *
	 * @throws \TYPO3\Docs\Exception\MissingStrategyException
	 * @return void
	 */
	public function importAll() {
		if (empty($this->strategy)) {
			throw new \TYPO3\Docs\Exception\MissingStrategyException('Missing Import Strategy', 1354627939);
		}

		$this->strategy->importAll();
	}

	/**
	 * @return \TYPO3\Docs\Service\Import\StrategyInterface
	 */
	public function getStrategy() {
		return $this->strategy;
	}

	/**
	 * @param \TYPO3\Docs\Service\Import\StrategyInterface $strategy
	 * @return \TYPO3\Docs\Service\ImportService
	 */
	public function setStrategy($strategy) {
		$this->strategy = $strategy;
		return $this;
	}

}
?>