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
 * $Id: HeaderViewHelper.php 64729 2012-07-20 16:04:08Z francois $
 */
class Tx_Svconnector_ViewHelpers_Be_HeaderViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {

	/**
	 * @var t3lib_PageRenderer
	 */
	private $pageRenderer;

	/**
	 * @return Tx_Svconnector_ViewHelpers_Be_HeaderViewHelper
	 */
	public function __construct() {
		$this->pageRenderer = $this->getDocInstance()->getPageRenderer();
	}

	/**
	 * Renders the view helper
	 *
	 * In this case, it actually renders nothing, but only loads stuff in the page header
	 *
	 * @param array $samples List of sample configurations
	 * @return void
	 */
	public function render($samples) {

			// Pass some settings to the JavaScript application
		$this->pageRenderer->addInlineSettingArray(
			'svconnector',
			array(
				'samples' => $samples
			)
		);
			// Load application specific JS
		$this->pageRenderer->addJsFile(t3lib_extMgm::extRelPath('svconnector') . 'Resources/Public/JavaScript/Module.js', 'text/javascript', FALSE);
	}
}

?>