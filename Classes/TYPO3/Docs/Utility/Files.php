<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with files
 *
 * @Flow\Scope("singleton")
 */
class Files  {

	/**
	 * Return a unix time of the last modification
	 *
	 * @param string $filePath
	 * @return int
	 */
	static public function getModificationTime($filePath) {
		$unixTime = 0;
		if (is_file($filePath)) {
			$unixTime = filemtime($filePath);
		}
		return $unixTime;
	}

	/**
	 * Return a unix time of the last modification from a remote file
	 *
	 * @throws \TYPO3\Docs\Exception\MissingFileException
	 * @param $uri
	 * @return int
	 */
	static public function getRemoteModificationTime($uri) {
		$handle = fopen($uri, "r");
		if (!$handle) {
			throw new \TYPO3\Docs\Exception\MissingFileException('Data source was not found at ' . $uri, 1345213889);
		}

		$metaData = stream_get_meta_data($handle);

		$unixTime = 0;
		foreach ($metaData['wrapper_data'] as $response) {
			if (substr(strtolower($response), 0, 15) == 'last-modified: ') {
				$unixTime = strtotime(substr($response, 15));
				break;
			}
		}
		fclose($handle);
		return $unixTime;
	}

	/**
	 * Write content to the file system. Create the directory of the file if this latter does not exist.
	 *
	 * @throws \TYPO3\Docs\Exception\WriteFileException
	 * @param string $file
	 * @param string $content
	 * @return int
	 */
	static public function write($file, $content) {

		$directory = dirname($file);
		if (!is_dir($directory)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($directory);
		}

		// write content
		$result = file_put_contents($file, $content);

		if (!$result) {
			throw new \TYPO3\Docs\Exception\WriteFileException('Exception thrown #1300100506: not possible to write file at "' . $file . '"', 1300100506);
		}

		return $result;
	}
}

?>