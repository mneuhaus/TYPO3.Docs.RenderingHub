<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model\Task;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask;
use TYPO3\Docs\RenderingHub\Domain\Model\Combo;
use TYPO3\Docs\RenderingHub\Domain\Model\Document;
use TYPO3\Docs\RenderingHub\Domain\Model\DocumentVariant;
use TYPO3\Docs\RenderingHub\Domain\Model\Package;
use TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository;
use TYPO3\Docs\RenderingHub\Domain\Repository\DocumentVariantRepository;
use TYPO3\Docs\RenderingHub\Domain\Repository\PackageRepository;
use TYPO3\Docs\RenderingHub\Utility\Files;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Exception\ObjectValidationFailedException;

/**
 * @Flow\Entity
 */
class CreatePackageTask extends AbstractTask {
    /**
     * @Flow\Inject(setting="importStrategies.ter")
     * @Flow\Transient
     * @var array
     */
    protected $settings;

    /**
     * @Flow\Inject
     * @var DocumentRepository
     */
    protected $documentRepository;

    /**
     * @Flow\Inject
     * @var PackageRepository
     */
    protected $packageRepository;

    public function executeTask() {
        try {
            $packageData = $this->getCombo()->getData();

            $package = $this->packageRepository->findPackage($packageData['identifier'], $packageData['parent']);
            if ($package === NULL) {
                $package = new Package();
                $package->setIdentifier($packageData['identifier']);
                $package->setParent($this->packageRepository->findOneByIdentifier($packageData['parent']));
            }
            $package->setSource($packageData['source']);
            $package->setTitle($packageData['title']);

            foreach ($packageData['documents'] as $document) {
                $this->createDocument($document, $package);
            }

            $this->addOrUpdate($package);
            $this->persistenceManager->persistAll();
        } catch(ObjectValidationFailedException $exception) {
            $this->status = self::FAILED;
        }
        $this->status = self::DONE;
    }

    public function createDocument($documentData, $package) {
        $document = $this->documentRepository->findDocument($package, $documentData['type']);

        if ($document === NULL) {
            $document = new Document();
            $document->setPackage($package);
            $document->setType($documentData['type']);
        }
        $document->setTitle($documentData['title']);
        $document->setSource($documentData['source']);
        $this->addOrUpdate($document);

        foreach ($documentData['variants'] as $documentVariantData) {
            $documentVariant = new DocumentVariant();
            $documentVariant->setDocument($document);
            $documentVariant->setLocale($documentVariantData['locale']);
            $documentVariant->setVersion($documentVariantData['version']);
            $this->addOrUpdate($documentVariant);

        }
    }

    public function addOrUpdate($entity) {
        if ($this->persistenceManager->isNewObject($entity)) {
            $this->persistenceManager->add($entity, get_class($entity));
        } else {
            $this->persistenceManager->update($entity, get_class($entity));
        }
    }

}