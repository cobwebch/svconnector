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
 * This class is used to load application-specific files (JS and CSS) for the BE module
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class Tx_Svconnector_ViewHelpers_Be_ContainerViewHelper extends Tx_Fluid_ViewHelpers_Be_ContainerViewHelper {
	/**
	 * Render start page with template.php and pageTitle
	 *
	 * @param string  $pageTitle title tag of the module. Not required by default, as BE modules are shown in a frame
	 * @param boolean $enableJumpToUrl If TRUE, includes "jumpTpUrl" javascript function required by ActionMenu. Defaults to TRUE
	 * @param boolean $enableClickMenu If TRUE, loads clickmenu.js required by BE context menus. Defaults to TRUE
	 * @param boolean $loadPrototype specifies whether to load prototype library. Defaults to TRUE
	 * @param boolean $loadScriptaculous specifies whether to load scriptaculous libraries. Defaults to FALSE
	 * @param string  $scriptaculousModule additionales modules for scriptaculous
	 * @param boolean $loadExtJs specifies whether to load ExtJS library. Defaults to FALSE
	 * @param boolean $loadExtJsTheme whether to load ExtJS "grey" theme. Defaults to FALSE
	 * @param string  $extJsAdapter load alternative adapter (ext-base is default adapter)
	 * @param boolean $enableExtJsDebug if TRUE, debug version of ExtJS is loaded. Use this for development only
	 * @param string $addCssFile Custom CSS file to be loaded
	 * @param string $addJsFile Custom JavaScript file to be loaded
	 * @param array $samples Code samples to be loaded as JS settings (specific to "svconnector" extension)
	 * @return string
	 * @see template
	 * @see t3lib_PageRenderer
	 */
	public function render($pageTitle = '', $enableJumpToUrl = TRUE, $enableClickMenu = TRUE, $loadPrototype = TRUE, $loadScriptaculous = FALSE, $scriptaculousModule = '', $loadExtJs = FALSE, $loadExtJsTheme = TRUE, $extJsAdapter = '', $enableExtJsDebug = FALSE, $addCssFile = NULL, $addJsFile = NULL, $samples = array()) {
		$pageRenderer = $this->getDocInstance()->getPageRenderer();

			// Pass some settings to the JavaScript application
		$pageRenderer->addInlineSettingArray(
			'svconnector',
			array(
				'samples' => $samples
			)
		);

		$output = parent::render($pageTitle, $enableJumpToUrl, $enableClickMenu, $loadPrototype, $loadScriptaculous, $scriptaculousModule, $loadExtJs, $loadExtJsTheme, $extJsAdapter, $enableExtJsDebug, $addCssFile, $addJsFile);
		return $output;
	}
}

?>