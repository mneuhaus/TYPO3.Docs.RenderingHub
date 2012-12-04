<?php
namespace TYPO3\Docs\Finder\Uri\Git;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class dealing with Uri coming from Git packages
 */
class Typo3CmsDocumentationCase extends \TYPO3\Docs\Finder\Uri\Git\AbstractCase {

	/**
	 * @param \TYPO3\Docs\Finder\Uri\Git\AbstractCase $nextCase
	 * @return void
	 */
	public function setSuccessor(\TYPO3\Docs\Finder\Uri\Git\AbstractCase $nextCase) {
		$this->successor = $nextCase;
	}

	/**
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string
	 */
	public function handle($package) {

		$repositoryUri = ltrim($package->getRepository(), '/');
		$parts = explode('/', $repositoryUri);

		if ($parts[0] == 'Documentation' && $parts[1] == 'TYPO3') {
			// Remove the .git suffix
			$documentBaseName = str_replace('.git', '', array_pop($parts));

			$languageSegment = '';
			if (preg_match('/\.(.{2}_.{2})$/is', $documentBaseName, $matches)) {
				$languageSuffix = $matches[0];
				$languageSegment = strtolower($matches[1]) . '/';
				$documentBaseName = str_replace($languageSuffix, '', $documentBaseName);
			}
			$documentSuffix = ucfirst(array_pop($parts));
			$result = sprintf('typo3cms/%s%s/%s%s',
				$documentBaseName,
				$documentSuffix,
				$languageSegment,
				$package->getVersion()
			);
		} else if ($this->successor != NULL) {

			$result = $this->successor->handle($package);
		}
		return $result;
	}
}

?>