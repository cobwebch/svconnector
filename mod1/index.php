<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Francois <typo3.ch>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

$LANG->includeLLFile('EXT:svconnector/mod1/locallang.xml');
$BE_USER->modAccess($MCONF, 1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]


/**
 * BE module to test connector services
 *
 * @author		Francois Suter <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class  tx_svconnector_module1 extends t3lib_SCbase {
	public $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	public function init()	{
		parent::init();
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	public function menuConfig()	{
		$this->MOD_MENU = array(
			'function' => array(
				'test_service' => $GLOBALS['LANG']->getLL('function.test_service'),
//				'2' => $LANG->getLL('function2'),
//				'3' => $LANG->getLL('function3'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Writes the content to $this->content.
	 *
	 * @return	void
	 */
	public function main()	{
			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

			// Initialize doc
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->setModuleTemplate(t3lib_extMgm::extPath('svconnector') . 'mod1/mod_template.html');
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$docHeaderButtons = $this->getButtons();

		if (($this->id && $access) || ($GLOBALS['BE_USER']->user['admin'] && !$this->id))	{

				// Draw the form
			$this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';
				// Render content:
			$this->moduleContent();
		} else {
				// If no access or if ID == zero
			$docHeaderButtons['save'] = '';
			$this->content.=$this->doc->spacer(10);
		}

			// Compile document
		$markers = array();
		$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu(0, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
		$markers['CONTENT'] = $this->content;

			// Build the <body> for the module
		$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
		$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	public function printContent()	{
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	protected function moduleContent()	{
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 'test_service':
				$content = $this->connectorServicesTestScreen();
				$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('header.test_service'), $content, 0, 1);
				break;
		}
	}

	/*
	 * This method generates the screen where connectors services can be tested
	 *
	 * @return	void
	 */
	protected function connectorServicesTestScreen() {
			// Load the necessary JavaScript
			/** @var $pageRenderer t3lib_PageRenderer */
		$pageRenderer = $this->doc->getPageRenderer();
		$pageRenderer->loadExtJS();
		$pageRenderer->addJsFile(t3lib_extMgm::extRelPath('svconnector') . 'res/service_test.js');

		$content = '';
			// Get a list of all available services
		$options = array();
		foreach ($GLOBALS['T3_SERVICES']['connector'] as $serviceKey => $serviceInfo) {
			$serviceObject = t3lib_div::makeInstance($serviceInfo['className']);
			if ($serviceObject->init()) {
				$options[$serviceKey] = $serviceInfo['title'];
			}
		}
			// No connector services are available, display error message
		if (count($options) == 0) {

			// Assemble the form for choosing a particular service and defining its parameters
		} else {
			$content .= '<p>' . $GLOBALS['LANG']->getLL('service') . '</p>';
			$content .= '<p><select name="tx_svconnector_mod1[service]" id="tx_svconnector_mod1_service">';
			foreach ($options as $key => $value) {
				$content .= '<option value="' . $key . '">' . $value . ' (' . $key . ')' . '</option>';
			}
			$content .= '</select></p>';
			$content .= '<p>' . $GLOBALS['LANG']->getLL('parameters') . '</p>';
			$content .= '<p><textarea name="tx_svconnector_mod1[parameters]" cols="50" rows="6" id="tx_svconnector_mod1_parameters"></textarea></p>';
			$content .= '<input type="button" name="submit" value="' . $GLOBALS['LANG']->getLL('test') . '" onclick="testService()" />';
			$content .= '<div id="tx_svconnector_resultarea"></div>';
		}
		return $content;
	}

	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return	array	all available buttons as an assoc. array
	 */
	protected function getButtons()	{

		$buttons = array(
			'csh' => '',
			'shortcut' => '',
		);
			// CSH
		$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

			// Shortcut
		if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
			$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
		}

		return $buttons;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svconnector/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svconnector/mod1/index.php']);
}


	// Make instance:
$SOBE = t3lib_div::makeInstance('tx_svconnector_module1');
$SOBE->init();

	// Include files?
foreach ($SOBE->include_once as $INC_FILE) {
	include_once($INC_FILE);
}

$SOBE->main();
$SOBE->printContent();

?>