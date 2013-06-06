<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with message on the console
 *
 * @Flow\Scope("singleton")
 */
class CommandMessage  {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\PackageRepository
	 */
	protected $packageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * Generates and returns a message to be displayed on the console upon importing all
	 *
	 * @param array $repositoryTypes of synchronization.
	 * @return string
	 */
	public function getImportAllMessage(array $repositoryTypes) {

		$action = $message = '';
		foreach ($repositoryTypes as $repositoryType) {
			$numberOfDocuments = $this->packageRepository->countPackageToProcess($repositoryType);

			// Makes sense to only display a message if more than 100 documents
			if ($numberOfDocuments > 100) {
				$action .= sprintf("- render %s new document(s) from the %s.typo3.org\n",
					$numberOfDocuments,
					$repositoryType
				);
			}
		}

		if ($action) {
			$message = <<< EOF
You are going to perform the following actions:

$action
Note: consider adding a limit if the number of items is too big to avoid the system to run out of memory.

./flow3 document:importall ter --limit 100

Aye you sure of that?\nPress y or n:
EOF;
		}
		return $message;
	}

	/**
	 * Generates and returns a message to be displayed on the console upon updating
	 *
	 * @return string
	 */
	public function getUpdateAllMessage() {

		$message = <<< EOF
You are going to perform the following actions:

- update {$this->documentRepository->countAll()} new document(s)

Note: consider adding a limit if the number of items is too big to avoid the system to run out of memory.

./flow3 document:importall ter --limit 100

Aye you sure of that?\nPress y or n:
EOF;
		return $message;
	}

}

?>