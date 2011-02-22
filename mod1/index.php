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
			// Get submitted variables
		$moduleVariables = t3lib_div::_GPmerged('tx_svconnector_mod1');
			// Make sure the "service" variable is initialized
		if (!isset($moduleVariables['service'])) {
			$moduleVariables['service'] = '';
		}

			// Load the necessary JavaScript
			/** @var $pageRenderer t3lib_PageRenderer */
/*
		$pageRenderer = $this->doc->getPageRenderer();
		$pageRenderer->loadExtJS();
		$pageRenderer->addJsFile(t3lib_extMgm::extRelPath('svconnector') . 'res/service_test.js');
 */

		$content = '';
			// Get a list of all available services
		$options = array();
		foreach ($GLOBALS['T3_SERVICES']['connector'] as $serviceKey => $serviceInfo) {
			$serviceObject = t3lib_div::makeInstance($serviceInfo['className']);
				// If the service is available, add it to the list
			if ($serviceObject->init()) {
				$options[$serviceKey] = $serviceInfo['title'];

				// If not, display a warning
			} else {
					/** @var $messageObject t3lib_FlashMessage */
				$messageObject = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					sprintf($GLOBALS['LANG']->getLL('serviceNotAvailable'), $serviceInfo['className']),
					'',
					t3lib_FlashMessage::WARNING
				);
				t3lib_FlashMessageQueue::addMessage($messageObject);
			}
		}
			// No connector services are available, display error message
		if (count($options) == 0) {
				/** @var $messageObject t3lib_FlashMessage */
			$messageObject = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$GLOBALS['LANG']->getLL('noServices'),
				'',
				t3lib_FlashMessage::NOTICE
			);
			$content .= $messageObject->render();

			// Assemble the form for choosing a particular service and defining its parameters
		} else {
			$content .= '<p>' . $GLOBALS['LANG']->getLL('service') . '</p>';
			$content .= $this->doc->spacer(5);
			$content .= '<p><select name="tx_svconnector_mod1[service]" id="tx_svconnector_mod1_service">';
			foreach ($options as $key => $value) {
				$selected = '';
				if ($key == $moduleVariables['service']) {
					$selected = ' selected="selected"';
				}
				$content .= '<option value="' . $key . '"' . $selected . '>' . $value . ' (' . $key . ')' . '</option>';
			}
			$content .= '</select></p>';
			$content .= $this->doc->spacer(10);
			$content .= '<p>' . $GLOBALS['LANG']->getLL('parameters') . '</p>';
			$content .= $this->doc->spacer(5);
			$content .= '<p><textarea name="tx_svconnector_mod1[parameters]" cols="50" rows="6" id="tx_svconnector_mod1_parameters">' . ((isset($moduleVariables['parameters'])) ? $moduleVariables['parameters'] : '') . '</textarea></p>';
			$content .= $this->doc->spacer(10);
			$content .= '<input type="submit" name="submit" value="' . $GLOBALS['LANG']->getLL('test') . '" />';
		}

			// If the form was submitted, process the request
		if (!empty($moduleVariables['service']) && !empty($moduleVariables['parameters'])) {
			$content .= $this->performServiceTest($moduleVariables);
		}
		return $content;
	}

	/**
	 * This method fires the requested query via the given connector service
	 * It loads the results into the response body.
	 *
	 * @param	array	$moduleVariables: variables with which the module was called
	 * @return	void
	 */
	public function performServiceTest($moduleVariables) {
		$resultContent = '';

			/** @var $serviceObject tx_svconnector_base */
		$serviceObject = t3lib_div::makeInstance($moduleVariables['service']);
		if ($serviceObject->init()) {
			$parameters = $this->parseParameters($moduleVariables['parameters']);
			try {
				$result = $serviceObject->fetchRaw($parameters);
				$testResult = '';
				if (empty($result)) {
						/** @var $messageObject t3lib_FlashMessage */
					$messageObject = t3lib_div::makeInstance(
						't3lib_FlashMessage',
						$GLOBALS['LANG']->getLL('noResult'),
						'',
						t3lib_FlashMessage::INFO
					);
					$testResult = $messageObject->render();
				} else {
					if (is_array($result)) {
						$testResult = tx_svconnector_utility::dumpArray($result);
					} else {
						$testResult = '<pre>' . htmlspecialchars($result) . '</pre>';
					}
				}
			}
			catch (Exception $e) {
					/** @var $messageObject t3lib_FlashMessage */
				$messageObject = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					sprintf($GLOBALS['LANG']->getLL('serviceError'), $e->getMessage(), $e->getCode()),
					'',
					t3lib_FlashMessage::ERROR
				);
				t3lib_FlashMessageQueue::addMessage($messageObject);
			}
		}
		$resultContent = $this->doc->section($GLOBALS['LANG']->getLL('testResult'), $testResult);
		return $resultContent;
	}

	/**
	 * This method parses the parameters input string and transforms it into an array
	 * of key-value pairs
	 *
	 * @param	string	$parametersString: input string from the query variables
	 * @return	array	Array of key-value pairs
	 */
	protected function parseParameters($parametersString) {
		$parameters = array();
		$lines = t3lib_div::trimExplode("\n", $parametersString, TRUE);
		foreach ($lines as $aLine) {
			$lineParts = t3lib_div::trimExplode('=', $aLine, TRUE);
			$key = array_shift($lineParts);
			$value = implode('=', $lineParts);
				// Handle special case of value "tab"
			if ($value == '\t') {
				$value = "\t";
			}
			$parameters[$key] = $value;
		}
		return $parameters;
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
//		$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

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