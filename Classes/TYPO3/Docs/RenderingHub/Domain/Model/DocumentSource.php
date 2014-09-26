<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;
use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class DocumentSource {

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant
     * @ORM\ManyToOne(inversedBy="sources")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $variant;

    /**
     * Gets uri.
     *
     * @return string $uri
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Sets the uri.
     *
     * @param string $uri
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    /**
     * Gets variant.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant $variant
     */
    public function getVariant() {
        return $this->variant;
    }

    /**
     * Sets the variant.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant $variant
     */
    public function setVariant($variant) {
        $this->variant = $variant;
    }

}