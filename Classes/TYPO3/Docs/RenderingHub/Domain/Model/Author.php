<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Category
 *
 * @Flow\Entity
 */
class Author extends \TYPO3\Party\Domain\Model\Person {

	/**
	 * Documents from the author
	 *
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Document>
	 * @ORM\ManyToMany
	 */
	protected $documents;

	/**
	 * Get the author's documents
	 *
	 * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category> The category's documents
	 */
	public function getDocuments() {
		return $this->documents;
	}

	/**
	 * Adds a document to this author
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
	 * @return void
	 */
	public function addDocument(\TYPO3\Docs\RenderingHub\Domain\Model\Document $document) {
		$this->documents->add($document);
	}

	/**
	 * Removes a document from this author
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
	 * @return void
	 */
	public function removeDocument(\TYPO3\Docs\RenderingHub\Domain\Model\Document $document) {
		$this->documents->removeElement($document);
	}
}
?>