<?php
namespace TYPO3\Docs\RenderingHub\Finder\Uri\Git;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Uri coming from Git packages
 */
class Typo3CmsExtensionCase extends \TYPO3\Docs\RenderingHub\Finder\Uri\AbstractCase {

	/**
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string
	 */
	public function handle(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {

		$repositoryUri = ltrim($package->getRepository(), '/');
		$parts = explode('/', $repositoryUri);

		if ($parts[0] === 'TYPO3v4' && $parts[1] === 'Extensions') {
			// Remove the .git suffix
			$documentBaseName = str_replace('.git', '', array_pop($parts));
			$result = sprintf('/typo3cms/extensions/%s/%s',
				$documentBaseName,
				$package->getVersion()
			);
		} else {
			$result = $this->proceed($package);
		}

		return $result;
	}
}

?>