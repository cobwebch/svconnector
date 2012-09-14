<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Francois Suter (typo3@cobweb.ch)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This view helper is designed to output the result of the connection test appropriately, depending on its format
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class Tx_Svconnector_ViewHelpers_Be_ResultViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {

	/**
	 * Renders the view helper
	 *
	 * In this case, it renders whatever result the connection test returned
	 *
	 * @param mixed $result Result of the connection test
	 * @return string The rendered result
	 */
	public function render($result) {
			// If the result is an array, dump it in a formatted display
			// Otherwise display a preformatted string
		if (is_array($result)) {
			$content = tx_svconnector_utility::dumpArray($result);
		} else {
			$content = '<pre>' . htmlspecialchars($result) . '</pre>';
		}
		return $content;
	}
}

?>