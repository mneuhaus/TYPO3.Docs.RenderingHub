<?php
namespace TYPO3\Docs\RenderingHub\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Docs\RenderingHub\Domain\Model\Document;
use TYPO3\Flow\Annotations as Flow;

/**
 * A repository for Documentations
 *
 * @Flow\Scope("singleton")
 */
class DocumentRepository extends \TYPO3\Flow\Persistence\Repository {
	public function findDocument($package, $type) {
		$query = $this->createQuery();
		$query->matching($query->logicalAnd(
			$query->equals('package', $package),
			$query->equals('type', $type)
		));
		return $query->execute()->getFirst();
	}
}
?>