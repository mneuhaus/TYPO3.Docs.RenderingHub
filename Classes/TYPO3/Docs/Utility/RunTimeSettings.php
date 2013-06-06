<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Utility class dealing with run time settings
 *
 * @FLOW3\Scope("singleton")
 */
class RunTimeSettings  {

	/**
	 * An array of formats to be rendered(html, ebook, pdf, ...)
	 *
	 * @var array
	 */
	protected $formats = array('html');

	/**
	 * Tell whether to set a dry-run flag
	 *
	 * @var string
	 */
	protected $dryRun = FALSE;

	/**
	 * Whether to by pass some security check
	 *
	 * @var string
	 */
	protected $force = FALSE;

	/**
	 * Add a limit to spare the resource
	 *
	 * @var int
	 */
	protected $limit = 1000000000;

	/**
	 * @return string
	 */
	public function getDryRun() {
		return $this->dryRun;
	}

	/**
	 * @param string $dryRun
	 */
	public function setDryRun($dryRun) {
		$this->dryRun = $dryRun;
	}

	/**
	 * @return string
	 */
	public function getForce() {
		return $this->force;
	}

	/**
	 * @param string $force
	 */
	public function setForce($force) {
		$this->force = $force;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit) {
		if ($limit != 0) {
			$this->limit = $limit;
		}
	}

	/**
	 * @return array
	 */
	public function getFormats() {
		return $this->formats;
	}

	/**
	 * @param array $formats
	 */
	public function setFormats($formats) {
		if (is_string($formats)) {
			$formats = \TYPO3\FLOW3\Utility\Arrays::trimExplode(',', $formats);
		}
		$this->formats = $formats;
	}
}

?>