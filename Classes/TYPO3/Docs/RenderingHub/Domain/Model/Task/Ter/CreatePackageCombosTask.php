<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model\Task\Ter;

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

/**
 * @Flow\Entity
 */
class CreatePackageCombosTask extends AbstractTask {
    /**
     * @Flow\Inject(setting="importStrategies.ter")
     * @Flow\Transient
     * @var array
     */
    protected $settings;

    public function executeTask() {
        // $this->systemLogger->log('Ter: importing new set of packages, it will take a while...', LOG_INFO);

        $limit = 20;
        $counter = 0;
        /** @var $extension \SimpleXMLElement */
        foreach ($this->get() as $extension) {
            if ($counter >= $limit) {
                continue;
            }
            $counter++;

            $package = $this->createPackageArray($extension);
            if ($package === NULL) {
                continue;
            }
            $packageCombo = new Combo();
            $packageCombo->setName('Import Package from TER');
            $packageCombo->setData($package);
            $packageCombo->createTask('\TYPO3\Docs\RenderingHub\Domain\Model\Task\CreatePackageTask');
            $packageCombo->queue();
        }
    }

    public function createPackageArray($extension) {
        $versions = array();
        foreach ($extension->version as $version) {
            $versionNumber = (string)$version['version'];
            // a valid version should be defined
            if (version_compare($versionNumber, '0.0.0', '>')) {
                $versions[$versionNumber] = $version;
            }
        }

        if (empty($versions)) {
            return;
        }

        $latestVersion = end($versions);

        $package = array(
            'title' => (string)$latestVersion->title,
            'identifier' => (string)$extension['extensionkey'],
            'parent' => 'typo3.cms',
            'source' => 'ter',
            'documents' => array()
        );

        $package['documents'][] = array(
            'title' => (string)$latestVersion->title,
            'type' => 'manual',
            'source' => 'ter',
            'variants' => array()
        );

        foreach ($versions as $version) {
            $package['documents'][0]['variants'][] = array(
                'locale' => 'en_US',
                'version' => (string)$version['version']
            );
        }

        return $package;
    }

    /**
     * Returns a bunch of data coming from the data-source
     * Ter data-source is serialized in XML.
     * It will raise an exception if the data-source is not found.
     *
     * @throws \TYPO3\Docs\RenderingHub\Exception\XmlParsingException
     * @throws \TYPO3\Docs\RenderingHub\Exception\MissingDataSourceException
     * @return \SimpleXMLElement
     */
    public function get() {

        // Make sure the file exists
        if (!is_file($this->settings['datasource'])) {
            throw new \TYPO3\Docs\RenderingHub\Exception\MissingDataSourceException('There is no data source. File not found ' . $this->settings['datasource'], 1345549138);
        }

        // Transfer data from extensions.xml.gz to database:
        $unzippedExtensionsXML = implode('', @gzfile($this->settings['datasource']));

        /** @var $xml \SimpleXMLElement */
        $entries = new \SimpleXMLElement($unzippedExtensionsXML);
        if (!is_object($entries)) {
            throw new \TYPO3\Docs\RenderingHub\Exception\XmlParsingException('Error while parsing ' . $this->settings['datasource'], 1300783708);
        }

        return $entries;
    }

}