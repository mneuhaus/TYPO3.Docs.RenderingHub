<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;
use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class DocumentVariant {
    /**
     * @var string
     */
    const STATUS_RENDER = 'waiting-rendering';
    const STATUS_OK = 'OK';
    const STATUS_SYNC = 'waiting-sync';
    const STATUS_NOT_FOUND = 'documentation-not-found';

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $status = self::STATUS_RENDER;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\Document
     * @ORM\ManyToOne(inversedBy="variants")
     */
    protected $document;

    /**
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentSource>
     * @ORM\OneToMany(mappedBy="variant")
     */
    protected $sources;

    /**
     * @var \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentBuild>
     * @ORM\OneToMany(mappedBy="variant")
     */
    protected $builds;

    public function __toString() {
        return $this->document . ' [' . $this->locale . '/' . $this->version . ']';
    }

    /**
     * Add to the builds.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentBuild $build
     */
    public function addBuild($build) {
        $this->builds->add($build);
    }

    /**
     * Remove from builds.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentBuild $build
     */
    public function removeBuild($build) {
        $this->builds->remove($build);
    }

    /**
     * Gets builds.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentBuild> $builds
     */
    public function getBuilds() {
        return $this->builds;
    }

    /**
     * Sets the builds.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentBuild> $builds
     */
    public function setBuilds($builds) {
        $this->builds = $builds;
    }

    /**
     * Gets document.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
     */
    public function getDocument() {
        return $this->document;
    }

    /**
     * Sets the document.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
     */
    public function setDocument($document) {
        $this->document = $document;
    }

    /**
     * Gets locale.
     *
     * @return string $locale
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Sets the locale.
     *
     * @param string $locale
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Add to the sources.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentSource $source
     */
    public function addSource($source) {
        $this->sources->add($source);
    }

    /**
     * Remove from sources.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentSource $source
     */
    public function removeSource($source) {
        $this->sources->remove($source);
    }

    /**
     * Gets sources.
     *
     * @return \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentSource> $sources
     */
    public function getSources() {
        return $this->sources;
    }

    /**
     * Sets the sources.
     *
     * @param \Doctrine\Common\Collections\Collection<\TYPO3\Docs\RenderingHub\Domain\Model\DocumentSource> $sources
     */
    public function setSources($sources) {
        $this->sources = $sources;
    }

    /**
     * Gets status.
     *
     * @return string $status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Sets the status.
     *
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Gets version.
     *
     * @return string $version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Sets the version.
     *
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

}