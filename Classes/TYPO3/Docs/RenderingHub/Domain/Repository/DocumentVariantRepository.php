<?php
namespace TYPO3\Docs\RenderingHub\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Docs\RenderingHub\Domain\Model\Document;
use TYPO3\Flow\Annotations as Flow;

/**
 * A repository for Packages
 *
 * @Flow\Scope("singleton")
 */
class DocumentVariantRepository extends \TYPO3\Flow\Persistence\Repository {
	public function findDocumentVariant($document, $version, $locale) {
		$query = $this->createQuery();
		$query->matching($query->logicalAnd(
			$query->equals('document', $document),
			$query->equals('version', $version),
			$query->equals('locale', $locale)
		));
		return $query->execute()->getFirst();
	}
}
?>