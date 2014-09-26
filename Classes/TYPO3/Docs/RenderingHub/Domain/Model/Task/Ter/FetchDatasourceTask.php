<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model\Task\Ter;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask;
use TYPO3\Docs\RenderingHub\Utility\Files;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 */
class FetchDatasourceTask extends AbstractTask {
    /**
     * @Flow\Inject(setting="importStrategies.ter")
     * @Flow\Transient
     * @var array
     */
    protected $settings;

    public function executeTask() {
        $isCacheObsolete = $this->isCacheObsolete();

        // Update the datasource if needed
        if ($isCacheObsolete) {
            $this->write();
            $this->systemLogger->log('Ter: data source has been updated with success', LOG_INFO);
        }

        $this->status = self::DONE;
    }

    /**
     * Check whether the data source should be updated. This will happen
     * when extension data source on typo3.org is more recent that the local version.
     *
     * @return boolean
     */
    protected function isCacheObsolete() {
        $localUnixTime = Files::getModificationTime($this->settings['datasource']);
        $remoteUnixTime = Files::getRemoteModificationTime($this->settings['datasourceRemote']);
        return $remoteUnixTime > $localUnixTime;
    }

    /**
     * Update from typo3.org the latest version of the data source of extensions (AKA extensions.xml.gz).
     *
     * @return void
     */
    public function write() {
        Files::createDirectoryRecursively(dirname($this->settings['datasource']));
        $content = Files::getFileContents($this->settings['datasourceRemote']);
        Files::write($this->settings['datasource'], $content);
    }
}