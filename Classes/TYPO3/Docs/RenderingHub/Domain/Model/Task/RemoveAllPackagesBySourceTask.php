<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model\Task;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask;
use TYPO3\Docs\RenderingHub\Domain\Repository\PackageRepository;
use TYPO3\Docs\RenderingHub\Utility\Files;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 */
class RemoveAllPackagesBySourceTask extends AbstractTask {
    /**
     * @var string
     */
    protected $datasource;

    /**
     * @Flow\Inject
     * @var PackageRepository
     */
    protected $packageRepository;

    public function executeTask() {
        $query = $this->packageRepository->createQuery();
        // $query->matching($query->equals('source', $this->datasource));
        foreach ($query->execute() as $package) {
            $this->packageRepository->remove($package);
        }
    }

    /**
     * @param string $datasource
     */
    public function setDatasource($datasource) {
        $this->datasource = $datasource;
    }

    /**
     * @return string
     */
    public function getDatasource() {
        return $this->datasource;
    }
}