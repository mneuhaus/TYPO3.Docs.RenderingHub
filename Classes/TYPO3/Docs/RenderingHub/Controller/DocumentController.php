<?php
namespace TYPO3\Docs\RenderingHub\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use Flowpack\Expose\Controller\CrudController;
use TYPO3\Docs\RenderingHub\Domain\Model\User;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Security\AccountFactory;

/**
 * Standard controller for the TYPO3.Docs package
 *
 * @Flow\Scope("singleton")
 */
class DocumentController extends CrudController {
	/**
	 * @var string
	 */
	protected $entity = 'TYPO3\Docs\RenderingHub\Domain\Model\Document';
}

?>