<?php
namespace TYPO3\Docs\RenderingHub\Finder\Uri;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with a chain of responsibility
 */
abstract class AbstractCase {

	/** @var  AbstractCase */
	protected $successor;

	/**
	 * @param \TYPO3\Docs\RenderingHub\Finder\Uri\AbstractCase $nextCase
	 * @return void
	 */
	public function setSuccessor(\TYPO3\Docs\RenderingHub\Finder\Uri\AbstractCase $nextCase) {
		$this->successor = $nextCase;
	}

	/**
	 * Let successor handle the package, or return empty string if no successor
	 * is set.
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string
	 */
	protected function proceed(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {
		if ($this->successor !== NULL) {
			return $this->successor->handle($package);
		} else {
			return '';
		}
	}

	/**
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string
	 */
	abstract public function handle(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package);
}

?>