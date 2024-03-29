<?php
namespace TYPO3\Docs\RenderingHub\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with color cli
 * @see http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/
 *
 * @Flow\Scope("singleton")
 */
class ColorCli {

	/**
	 * @var array
	 */
	static $foreground_colors = array(
		'black' => '0;30',
		'dark_gray' => '1;30',
		'blue' => '0;34',
		'light_blue' => '1;34',
		'green' => '0;32',
		'light_green' => '1;32',
		'cyan' => '0;36',
		'light_cyan' => '1;36',
		'red' => '0;31',
		'light_red' => '1;31',
		'purple' => '0;35',
		'light_purple' => '1;35',
		'brown' => '0;33',
		'yellow' => '1;33',
		'light_gray' => '0;37',
		'white' => '1;37',
	);

	/**
	 * @var array
	 */
	static $background_colors = array(
		'black' => '40',
		'red' => '41',
		'green' => '42',
		'yellow' => '43',
		'blue' => '44',
		'magenta' => '45',
		'cyan' => '46',
		'light_gray' => '47',
	);

	/**
	 * Returns colored string
	 *
	 * @param $string
	 * @param string $foreground_color
	 * @param string $background_color
	 * @return string
	 */
	public static function getColoredString($string, $foreground_color = NULL, $background_color = NULL) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset(self::$foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset(self::$background_colors[$background_color])) {
			$colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .= $string . "\033[0m";

		return $colored_string;
	}

	/**
	 * Returns all foreground color names
	 *
	 * @return array
	 */
	public static function getForegroundColors() {
		return array_keys(self::$foreground_colors);
	}

	/**
	 * Returns all background color names
	 *
	 * @return array
	 */
	public static function getBackgroundColors() {
		return array_keys(self::$background_colors);
	}
}

?>