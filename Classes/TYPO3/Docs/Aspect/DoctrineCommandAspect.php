<?php

namespace TYPO3\Docs\Aspect;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Aspect
 */
class DoctrineCommandAspect {

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

	/**
	 * Add "enum" as additional mapping type for doctrine
	 *
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint
	 * @Flow\Before("method(TYPO3\Flow\Command\DoctrineCommandController->.*Command())")
	 * @return void
	 */
	public function addCustomType(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$platform = $this->entityManager->getConnection()->getDatabasePlatform();
		$platform->registerDoctrineTypeMapping('enum', 'string');
	}

}

?>
