<?php
namespace TYPO3\Docs\RenderingHub\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with the lock file
 *
 * @Flow\Scope("singleton")
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
	 * Settings injection
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->lockFile = $this->settings['lockFile'];

			// Make sure the existing file hierarchy exists
		\TYPO3\Flow\Utility\Files::createDirectoryRecursively($this->settings['buildDir']);
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