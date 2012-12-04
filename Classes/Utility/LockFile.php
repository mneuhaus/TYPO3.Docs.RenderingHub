<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Utility class dealing with the lock file
 *
 * @FLOW3\Scope("singleton")
 */
class LockFile {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var array
	 */
	protected $lockFile;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration();

		$this->lockFile = $this->settings['lockFile'];
	}

	/**
	 * Return whether the lock file exists
	 *
	 * @param boolean
	 */
	public function exists() {
		return file_exists($this->lockFile);
	}

	/**
	 * Create a lock file
	 *
	 * @param boolean
	 */
	public function create() {
		return touch($this->lockFile);
	}

	/**
	 * Remove the lock file
	 *
	 * @return bool
	 */
	public function remove() {
		$result = FALSE;
		if ($this->exists()) {
			$result = unlink($this->lockFile);
		}
		return $result;
	}
}

?>