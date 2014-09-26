<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * A Package
 * @Flow\Entity
 */
class Package {

    /**
     * @var string
     * @ORM\Column(length=150)
     */
    protected $title;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\Package
     * @ORM\ManyToOne(inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $parent;

    /**
     * The child categories
     *
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Package>
     * @ORM\OneToMany(mappedBy="parent")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Document>
     * @ORM\OneToMany(mappedBy="package")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $documents;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $source;

    /**
     * Constructs this locale object
     *
     * @param array $data
     * @api
     */
    public function __construct() {
        $this->children = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * TODO: Document this Method! ( __toString )
     */
    public function __toString() {
        return $this->title;
    }

    /**
     * Add to the children.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $child
     */
    public function addChild($child) {
        $this->children->add($child);
    }

    /**
     * Remove from children.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $child
     */
    public function removeChild($child) {
        $this->children->remove($child);
    }

    /**
     * Gets children.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Package> $children
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Sets the children.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Package> $children
     */
    public function setChildren($children) {
        $this->children = $children;
    }

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

    /**
     * Gets identifier.
     *
     * @return string $identifier
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Sets the identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Gets parent.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package $parent
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets the parent.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Gets title.
     *
     * @return string $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @param string $source
     */
    public function setSource($source) {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

}