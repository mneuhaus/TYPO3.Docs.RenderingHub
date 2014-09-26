<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;
use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class DocumentBuild {

    /**
     * @var string
     */
    protected $uris;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant
     * @ORM\ManyToOne(inversedBy="builds")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $variant;

    /**
     * Gets uris.
     *
     * @return string $uris
     */
    public function getUris() {
        return $this->uris;
    }

    /**
     * Sets the uris.
     *
     * @param string $uris
     */
    public function setUris($uris) {
        $this->uris = $uris;
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