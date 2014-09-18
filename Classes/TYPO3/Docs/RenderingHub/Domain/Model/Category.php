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
class Category {

    /**
     * The Category's title
     *
     * @var string
     * @Flow\Validate(type="NotEmpty")
     * @Flow\Validate(type="StringLength", options={ "minimum"=10, "maximum"=100 })
     * @ORM\Column(length=100)
     */
    protected $title = '';

    /**
     * The Category's parent
     *
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\Category
     * @ORM\ManyToOne(inversedBy="children")
     */
    protected $parent;

    /**
     * The child categories
     *
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category>
     * @ORM\OneToMany(mappedBy="parent")
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Document>
     * @ORM\ManyToMany
     */
    protected $documents;

    /**
     * Adds a child category to this category
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Category $child
     * @return void
     */
    public function addChildren(\TYPO3\Docs\RenderingHub\Domain\Model\Category $child) {
        $child->setParent($this);
        $this->children->add($child);
    }

    /**
     * Get the category's children
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category> The category's children
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Removes a child from this category
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Category $child
     * @return void
     */
    public function removeChildren(\TYPO3\Docs\RenderingHub\Domain\Model\Category $child) {
        $this->children->removeElement($child);
    }

    /**
     * Adds a document to this category
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
     * @return void
     */
    public function addDocument(\TYPO3\Docs\RenderingHub\Domain\Model\Document $document) {
        $this->documents->add($document);
    }

    /**
     * Removes a document from this category
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
     * @return void
     */
    public function removeDocument(\TYPO3\Docs\RenderingHub\Domain\Model\Document $document) {
        $this->documents->removeElement($document);
    }

    /**
     * Get the category's documents
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category> The category's documents
     */
    public function getDocuments() {
        return $this->documents;
    }

    /**
     * Get the category's parent
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Category The category's parent
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets this category's parent
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Category $parent The category's parent
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Category
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Gets the category's title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the category's title
     *
     * @param string $title The new title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

}