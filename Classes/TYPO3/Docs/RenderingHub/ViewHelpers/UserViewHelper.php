<?php
namespace TYPO3\Docs\RenderingHub\ViewHelpers;

use TYPO3\Flow\Annotations as Flow;

class UserViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 *
	 * @param string $currentUser
	 * @return string
	 */
	public function render($as = 'currentUser') {
		$templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
		$templateVariableContainer->add($as, $this->securityContext->getParty());
		$output = $this->renderChildren();
		$templateVariableContainer->remove($as);
		return $output;
	}
}