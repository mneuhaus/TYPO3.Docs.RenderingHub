<?php
namespace TYPO3\Docs\Hooks;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Hook controller for notifications from review.typo3.org
 *
 * @Flow\Scope("singleton")
 */
class ReviewTypo3OrgHandler implements HookHandlerInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\PackageRepository
	 */
	protected $packageRepository;

	/**
	 * Returns the repository type of packages handled by this handler
	 *
	 * @return string
	 */
	public function getRepositoryType() {
		return 'git';
	}

	/**
	 * Fetch packages using the available information
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $request
	 * @return \TYPO3\Docs\Domain\Model\Package[]
	 */
	public function getPackages(\TYPO3\Flow\Mvc\ActionRequest $request) {
		$requestContent = $request->getHttpRequest()->getContent();
		$data = json_decode($requestContent);
		if (!isset($data->version)) {
			$data->version = NULL;
		}
		return $this->packageRepository->findGitPackagesByPackageKey($data->packageKey, $data->version);
	}

}

?>