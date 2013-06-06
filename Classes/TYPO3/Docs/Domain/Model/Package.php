<?php

namespace TYPO3\Docs\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Package
 */
class Package {

	/**
	 * @var string
	 * @ORM\Column(length=150)
	 */
	protected $title;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $abstract;

	/**
	 * @var string
	 * @ORM\Column(length=30)
	 */
	protected $product;

	/**
	 * @var string
	 * @ORM\Column(length=30)
	 */
	protected $locale;

	/**
	 * @var string
	 * @ORM\Column(length=30)
	 */
	protected $type;

	/**
	 * @var string
	 * @ORM\Column(length=100)
	 */
	protected $packageKey;

	/**
	 * @var string
	 * @ORM\Column(length=255)
	 */
	protected $uri;

	/**
	 * @var string
	 * @ORM\Column(length=255)
	 */
	protected $repository;

	/**
	 * @var string
	 * @ORM\Column(length=100)
	 */
	protected $version;

	/**
	 * @var string
	 * @ORM\Column(length=30)
	 */
	protected $repositoryType;

	/**
	 * @var string
	 * @ORM\Column(length=100)
	 */
	protected $repositoryTag;

	/**
	 * Constructs this locale object
	 *
	 * @param array $data
	 * @api
	 */
	public function __construct($data = array()) {

		// lower case for every keys avoiding sensitive case problem
		// when data comes directly from the database
		$data = array_change_key_case($data, CASE_LOWER);

		$this->title = empty($data['title']) ? '' : $data['title'];
		$this->abstract = empty($data['abstract']) ? '' : $data['abstract'];
		$this->product = empty($data['product']) ? '' : $data['product'];
		$this->locale = empty($data['locale']) ? '' : $data['locale'];
		$this->type = empty($data['type']) ? '' : $data['type'];
		$this->packageKey = empty($data['packagekey']) ? '' : $data['packagekey'];
		$this->uri = empty($data['uri']) ? '' : $data['uri'];
		$this->repository = empty($data['repository']) ? '' : $data['repository'];
		$this->version = empty($data['version']) ? '' : $data['version'];
		$this->repositoryType = empty($data['repositorytype']) ? '' : $data['repositorytype'];
		$this->repositoryTag = empty($data['repositorytag']) ? '' : $data['repositorytag'];
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getAbstract() {
		return $this->abstract;
	}

	/**
	 * @param string $abstract
	 */
	public function setAbstract($abstract) {
		$this->abstract = $abstract;
	}

	/**
	 * @return string
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @param string $product
	 */
	public function setProduct($product) {
		$this->product = $product;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @param string $locale
	 */
	public function setLocale($locale) {
		$this->locale = $locale;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getPackageKey() {
		return $this->packageKey;
	}

	/**
	 * @param string $packageKey
	 */
	public function setPackageKey($packageKey) {
		$this->packageKey = $packageKey;
	}

	/**
	 * @return string
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * @param string $uri
	 */
	public function setUri($uri) {
		$this->uri = $uri;
	}

	/**
	 * @return string
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * @param string $repository
	 */
	public function setRepository($repository) {
		$this->repository = $repository;
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function getRepositoryType() {
		return $this->repositoryType;
	}

	/**
	 * @param string $repositoryType
	 */
	public function setRepositoryType($repositoryType) {
		$this->repositoryType = $repositoryType;
	}

	/**
	 * @return string
	 */
	public function getRepositoryTag() {
		return $this->repositoryTag;
	}

	/**
	 * @param string $repositoryTag
	 */
	public function setRepositoryTag($repositoryTag) {
		$this->repositoryTag = $repositoryTag;
	}
}

?>
