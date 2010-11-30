<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Francois Suter (Cobweb) <typo3@cobweb.ch>
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

/**
 * This method responds to BE AJAX request
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class tx_svconnector_Ajax {
	public $extKey = 'svconnector';
	/** @var $extConf array extension configuration */
	protected $extConf = array();

	public function __construct() {
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
	}

	/**
	 * This method fires the requested query via the given connector service
	 * It loads the results into the response body.
	 *
	 * @param	array		$params: empty array (yes, that's weird but true)
	 * @param	TYPO3AJAX	$ajaxObj: back-reference to the calling object
	 * @return	void
	 */
	public function query($params, TYPO3AJAX $ajaxObj) {
		$service = t3lib_div::_GP('service');
		/** @var $serviceObject tx_svconnector_base */
		$serviceObject = t3lib_div::makeInstance($service);
		if ($serviceObject->init()) {
			$parametersInput = urldecode(t3lib_div::_GP('parameters'));
			$parameters = $this->parseParameters($parametersInput);
			$result = $serviceObject->fetchArray($parameters);
				// Limit result size so that response is not too large
			$result = array_slice($result, 0, $this->extConf['test_limit'], TRUE);
			$ajaxObj->setContentFormat('json');
			$ajaxObj->setContent($result);
		}
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
			$lineParts = t3lib_div::trimExplode('=>', $aLine, TRUE);
			$value = $lineParts[1];
			if ($value == '\t') {
				$value = "\t";
			}
			$parameters[$lineParts[0]] = $value;
		}
		return $parameters;
	}
}
?>