<?php
namespace TYPO3\Docs\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "Blog".                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * A view helper to display a Gravatar
 *
 * = Examples =
 *
 * <code title="Simple">
 * <blog:gravatar email="{emailAddress}" default="http://domain.com/gravatar_default.gif" class="gravatar" />
 * </code>
 *
 * Output:
 * <img class="gravatar" src="http://www.gravatar.com/avatar/<hash>?d=http%3A%2F%2Fdomain.com%2Fgravatar_default.gif" />
 *
 */
class UpTimeViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {


	/**
	 * Render the link.
	 *
	 * @return string The rendered link
	 */
	public function render() {
		$stringToRender = $this->renderChildren();
		$result = round(($stringToRender / 86400), 2);
		return $result;
	}
}


?>