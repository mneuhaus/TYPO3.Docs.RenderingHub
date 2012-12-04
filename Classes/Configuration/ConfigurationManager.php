<?php
namespace TYPO3\Docs\Configuration;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class dealing with configuration
 *
 * @FLOW3\Scope("singleton")
 */
class ConfigurationManager {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Inject the settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = array();
		if (!empty($settings['defaultConfiguration'])) {
			$this->settings = $settings['defaultConfiguration'];
		}
	}

	/**
	 * Return configuration. The "global" configuration from package TYPO3.Docs will be merged with local configuration of the package
	 */
	public function getConfiguration() {
		return $this->settings;
		#$settings = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Docs');
		#return array_merge($settings['defaultConfiguration'], $this->settings);
	}
}

?>