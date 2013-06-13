<?php
namespace TYPO3\Docs\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Hooks controller for notifications, webhook style
 *
 * @Flow\Scope("singleton")
 */
class HooksController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * @var \TYPO3\Docs\Service\Build\JobService
	 */
	protected $buildService;

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('application/json');

	/**
	 * Index action
	 *
	 * @param string $type
	 * @return string
	 */
	public function indexAction($type = NULL) {
		$this->response->setHeader('Content-Type', 'application/json');

		$handlerObjectName = $this->objectManager->getCaseSensitiveObjectName('TYPO3\Docs\Hooks\\' . $type . 'Handler');
		if ($this->objectManager->isRegistered($handlerObjectName)) {
			/**
			 * @var \TYPO3\Docs\Hooks\HookHandlerInterface $handler
			 */
			$handler = $this->objectManager->get($handlerObjectName);
			$packages = $handler->getPackages($this->request);

			$documentServiceClassName = 'TYPO3\Docs\Service\Document\\' . ucfirst($handler->getRepositoryType()) . 'Service';
			$documentService = $this->objectManager->get($documentServiceClassName);
			foreach ($packages as $package) {
				$document = $documentService->create($package);
				$documentService->build($document);
			}

			return json_encode(array('result' => 'Queued rendering for package ' . $package->getPackageKey()));
		} else {
			$this->response->setStatus(400);
			return json_encode('invalid type given: ' . $type);
		}
	}

	/**
	 * Queue a document for rendering
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return void
	 */
	public function queueForRendering(\TYPO3\Docs\Domain\Model\Document $document) {
	}

}

?>