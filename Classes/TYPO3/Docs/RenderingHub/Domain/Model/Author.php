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
     * @ORM\ManyToMany(inversedBy="authors")
     */
    protected $documents;

    /**
     * Add to the documents.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
     */
    public function addDocument($document) {
        $this->documents->add($document);
    }

    /**
     * Remove from documents.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
     */
    public function removeDocument($document) {
        $this->documents->remove($document);
    }

    /**
     * Gets documents.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Document> $documents
     */
    public function getDocuments() {
        return $this->documents;
    }

    /**
     * Sets the documents.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Document> $documents
     */
    public function setDocuments($documents) {
        $this->documents = $documents;
    }

}