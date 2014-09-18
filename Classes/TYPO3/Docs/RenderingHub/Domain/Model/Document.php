<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Document
 *
 * @Flow\Entity
 */
class Document {

    /**
     * @Flow\Inject
     * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
     */
    protected $documentRepository;

    /**
     * Title of the document
     *
     * @var string
     * @Flow\Validate(type="NotEmpty")
     * @Flow\Validate(type="StringLength", options={ "minimum"=3, "maximum"=150 })
     * @ORM\Column(length=255)
     */
    protected $title;

    /**
     * Abstract
     *
     * @var string
     * @ORM\Column(type="text")
     */
    protected $abstract = '';

    /**
     * List of authors
     *
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Author>
     * NOTE: do we need a more complete Person model, e.g. including an employer?
     * @ORM\ManyToMany(inversedBy="documents")
     */
    protected $authors;

    /**
     * Categories the document belongs to
     *
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category>
     * @ORM\ManyToMany(inversedBy="")
     */
    protected $categories;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\Package
     * @ORM\ManyToOne(inversedBy="documents")
     */
    protected $package;

    /**
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant>
     * @ORM\OneToMany(mappedBy="document")
     */
    protected $variants;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $source;

    public function __toString() {
        return $this->title;
    }

    /**
     * Gets abstract.
     *
     * @return string $abstract
     */
    public function getAbstract() {
        return $this->abstract;
    }

    /**
     * Sets the abstract.
     *
     * @param string $abstract
     */
    public function setAbstract($abstract) {
        $this->abstract = $abstract;
    }

    /**
     * Add to the authors.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Author $author
     */
    public function addAuthor($author) {
        $this->authors->add($author);
    }

    /**
     * Remove from authors.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Author $author
     */
    public function removeAuthor($author) {
        $this->authors->remove($author);
    }

    /**
     * Gets authors.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Author> $authors
     */
    public function getAuthors() {
        return $this->authors;
    }

    /**
     * Sets the authors.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Author> $authors
     */
    public function setAuthors($authors) {
        $this->authors = $authors;
    }

    /**
     * Gets categories.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category> $categories
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Sets the categories.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\Category> $categories
     */
    public function setCategories($categories) {
        $this->categories = $categories;
    }

    /**
     * Add to the categories.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Category $category
     */
    public function addCategory($category) {
        $this->categories->add($category);
    }

    /**
     * Remove from categories.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Category $category
     */
    public function removeCategory($category) {
        $this->categories->remove($category);
    }

    /**
     * @return boolean
     */
    public function getIsOk() {
        return $this->getStatus() === self::STATUS_OK;
    }

    /**
     * @return boolean
     */
    public function getIsProcessing() {
        return $this->getStatus() === self::STATUS_RENDER || $this->getStatus() === self::STATUS_SYNC;
    }

    /**
     * Gets the locale
     *
     * @return string
     */
    public function getLocaleObject() {
        return new \TYPO3\Flow\I18n\Locale($this->locale);
    }

    /**
     * Gets package.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     * Sets the package.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
     */
    public function setPackage($package) {
        $this->package = $package;
    }

    /**
     * Gets source.
     *
     * @return string $source
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * Sets the source.
     *
     * @param string $source
     */
    public function setSource($source) {
        $this->source = $source;
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
     * Gets type.
     *
     * @return string $type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Gets the URI Object
     *
     * @return string
     */
    public function getUriObject() {
        return new \TYPO3\Flow\Http\Uri($this->uri);
    }

    /**
     * Add to the variants.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant $variant
     */
    public function addVariant($variant) {
        $this->variants->add($variant);
    }

    /**
     * Remove from variants.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant $variant
     */
    public function removeVariant($variant) {
        $this->variants->remove($variant);
    }

    /**
     * Gets variants.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant> $variants
     */
    public function getVariants() {
        return $this->variants;
    }

    /**
     * Sets the variants.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant> $variants
     */
    public function setVariants($variants) {
        $this->variants = $variants;
    }

    /**
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package
     */
    public function toPackage() {
        $package = new \TYPO3\Docs\RenderingHub\Domain\Model\Package();
        $ref = new \ReflectionObject($package);
        $properties = $ref->getProperties();
        foreach ($properties as $property) {    $property = $property->getName();
            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);
            $value = call_user_func(array($this,
            	$getter
            ));
            call_user_func_array(array($package,
            	$setter
            ), array($value
            ));
        }
        return $package;
    }

}