<?php
namespace TYPO3\Docs\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Document
 *
 * @FLOW3\Entity
 */
class Document {

	/**
	 * Title of the document
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @FLOW3\Validate(type="StringLength", options={ "minimum"=3, "maximum"=150 })
	 * @ORM\Column(length=255)
	 */
	protected $title = '';

	/**
	 * Abstract
	 *
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $abstract = '';

	/**
	 * Type of document (manual, book?)
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @FLOW3\Validate(type="StringLength", options={ "minimum"=3, "maximum"=100 })
	 * @ORM\Column(length=100)
	 */
	protected $type = '';

	/**
	 * Version number
	 * Should stick to the conventions of version_compare(), see http://php.net/version_compare
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @ORM\Column(length=30)
	 */
	protected $version = '';

	/**
	 * Status code of the document.
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @ORM\Column(length=50, columnDefinition="ENUM('ok', 'documentation-not-found', 'ok-with-warnings', 'error-parsing', 'waiting-rendering', 'waiting-sync')")
	 */
	protected $status = '';

	/**
	 * Date and time of the rendering
	 *
	 * @var \DateTime
	 * @FLOW3\Validate(type="NotEmpty")
	 */
	protected $generationDate = '';

	/**
	 * Locale of the document (must be a valid locale)
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @FLOW3\Validate(type="LocaleIdentifier")
	 * @ORM\Column(length=50)
	 */
	protected $locale = '';

	/**
	 * Product to which the documentation belongs, e.g. "TYPO3", "FLOW3"
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @FLOW3\Validate(type="StringLength", options={ "minimum"=3, "maximum"=20 })
	 * @ORM\Column(length=20)
	 */
	protected $product = '';

	/**
	 * Package key to which the documentation belongs
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @FLOW3\Validate(type="StringLength", options={ "minimum"=3, "maximum"=100 })
	 * @ORM\Column(length=100)
	 */
	protected $packageKey = '';

	/**
	 * URI at which the document is available
	 *
	 * @var string
	 * @FLOW3\Validate(type="NotEmpty")
	 * @FLOW3\Validate(type="StringLength", options={ "maximum"=255 })
	 * @ORM\Column(length=255, unique=true)
	 */
	protected $uri = '';

	/**
	 * URI at which the document is also available
	 *
	 * @var string
	 * @FLOW3\Validate(type="StringLength", options={ "maximum"=255 })
	 * @ORM\Column(length=255)
	 */
	protected $uriAlias = '';

	/**
	 * The source URI where the package comes from.
	 *
	 * @var string
	 * @FLOW3\Validate(type="StringLength", options={ "maximum"=255 })
	 * @ORM\Column(length=255)
	 */
	protected $repository = '';

	/**
	 * The tag name of a repository for Git Document
	 *
	 * @var string
	 * @FLOW3\Validate(type="StringLength", options={ "maximum"=255 })
	 * @ORM\Column(length=255)
	 */
	protected $repositoryTag = '';

	/**
	 * Repository type of the document (ter, git)
	 *
	 * @var string
	 * @ORM\Column(length=100)
	 */
	protected $repositoryType = '';

	/**
	 * Get package remote file
	 *
	 * @var string
	 * @FLOW3\Validate(type="StringLength", options={ "maximum"=150 })
	 * @ORM\Column(length=150)
	 */
	protected $packageFile = '';

	/**
	 * List of authors
	 *
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\Domain\Model\Author>
	 * NOTE: do we need a more complete Person model, e.g. including an employer?
	 * @ORM\ManyToMany
	 */
	protected $authors;

	/**
	 * Categories the document belongs to
	 *
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\Domain\Model\Category>
	 * @ORM\ManyToMany
	 */
	protected $categories;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * Sets the abstract
	 *
	 * @param string $abstract
	 */
	public function setAbstract($abstract) {
		$this->abstract = $abstract;
	}

	/**
	 * Gets the abstract
	 *
	 * @return string
	 */
	public function getAbstract() {
		return $this->abstract;
	}

	/**
	 * Sets the generation date and time
	 *
	 * @param \DateTime $generationDate
	 */
	public function setGenerationDate($generationDate) {
		$this->generationDate = $generationDate;
	}

	/**
	 * Gets the generation date and time
	 *
	 * @return \DateTime
	 */
	public function getGenerationDate() {
		return $this->generationDate;
	}

	/**
	 * Sets the locale
	 *
	 * @param string $locale
	 */
	public function setLocale($locale) {
		$this->locale = $locale;
	}

	/**
	 * Gets the locale
	 *
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * Gets the locale
	 *
	 * @return string
	 */
	public function getLocaleObject() {
		return new \TYPO3\FLOW3\I18n\Locale($this->locale);
	}

	/**
	 * Sets the package key
	 *
	 * @param string $packageKey
	 */
	public function setPackageKey($packageKey) {
		$this->packageKey = $packageKey;
	}

	/**
	 * Gets the package key
	 *
	 * @return string
	 */
	public function getPackageKey() {
		return $this->packageKey;
	}

	/**
	 * Sets the product name
	 *
	 * @param string $product
	 */
	public function setProduct($product) {
		$this->product = $product;
	}

	/**
	 * Gets the product name
	 *
	 * @return string
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Gets the title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the type
	 *
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Gets the type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Set the URI
	 *
	 * @param string $uri A URI
	 */
	public function setUri($uri) {
		$this->uri = $uri;
	}

	/**
	 * Gets the URI
	 *
	 * @return string
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * Gets the URI Object
	 *
	 * @return string
	 */
	public function getUriObject() {
		return new \TYPO3\FLOW3\Http\Uri($this->uri);
	}

	/**
	 * Sets the version number
	 *
	 * @param string $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * Gets the version number
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Get the document's authors
	 *
	 * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\Domain\Model\Author> The document's authors
	 */
	public function getAuthors() {
		return $this->authors;
	}

	/**
	 * Adds an author to this document
	 *
	 * @param \TYPO3\Docs\Domain\Model\Author $author
	 * @return void
	 */
	public function addAuthor(\TYPO3\Docs\Domain\Model\Author $author) {
		$this->authors->add($author);
	}

	/**
	 * Removes an author from this document
	 *
	 * @param \TYPO3\Docs\Domain\Model\Author $author
	 * @return void
	 */
	public function removeAuthor(\TYPO3\Docs\Domain\Model\Author $author) {
		$this->authors->removeElement($author);
	}

	/**
	 * Get the document's categories
	 *
	 * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\Domain\Model\Category> The document's categories
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * Adds a category to this document
	 *
	 * @param \TYPO3\Docs\Domain\Model\Category $category
	 * @return void
	 */
	public function addCategory(\TYPO3\Docs\Domain\Model\Category $category) {
		$this->categories->add($category);
	}

	/**
	 * Removes a category from this document
	 *
	 * @param \TYPO3\Docs\Domain\Model\Category $category
	 * @return void
	 */
	public function removeCategory(\TYPO3\Docs\Domain\Model\Category $category) {
		$this->categories->removeElement($category);
	}

	/**
	 * Get the status from the document
	 *
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set a status to the document
	 *
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * Get the repository type of the document
	 *
	 * @return string
	 */
	public function getRepositoryType() {
		return $this->repositoryType;
	}

	/**
	 * Set the repository type
	 *
	 * @param string $repositoryType
	 */
	public function setRepositoryType($repositoryType) {
		$this->repositoryType = $repositoryType;
	}

	/**
	 * Get the source of the document
	 *
	 * @return string
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * Set the source of the document
	 *
	 * @param string $repository
	 */
	public function setRepository($repository) {
		$this->repository = $repository;
	}

	/**
	 * Get the repository tag
	 *
	 * @return string
	 */
	public function getRepositoryTag() {
		return $this->repositoryTag;
	}

	/**
	 * Set the repository tag
	 *
	 * @param string $repositoryTag
	 */
	public function setRepositoryTag($repositoryTag) {
		$this->repositoryTag = $repositoryTag;
	}

	/**
	 * Get package remote file
	 *
	 * @return string
	 */
	public function getPackageFile() {
		return $this->packageFile;
	}

	/**
	 * Set package remote file
	 *
	 * @param string $packageFile
	 */
	public function setPackageFile($packageFile) {
		$this->packageFile = $packageFile;
	}

	/**
	 * @return string
	 */
	public function getUriAlias() {
		return $this->uriAlias;
	}

	/**
	 * @param string $uriAlias
	 */
	public function setUriAlias($uriAlias) {
		$this->uriAlias = $uriAlias;
	}

	/**
	 * @return \TYPO3\Docs\Build\Domain\Model\Package
	 */
	public function toPackage() {
		$package = new \TYPO3\Docs\Build\Domain\Model\Package();

		$ref = new \ReflectionObject($package);
		$properties = $ref->getProperties();
		foreach ($properties as $property) {
			$property = $property->getName();
			$setter = 'set' . ucfirst($property);
			$getter = 'get' . ucfirst($property);

			$value = call_user_func(array($this, $getter));
			call_user_func_array(array($package, $setter), array($value));
		}
		return $package;
	}
}

?>