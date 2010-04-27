<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2008 Francois Suter (Cobweb) <typo3@cobweb.ch>
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

require_once(PATH_t3lib.'class.t3lib_svbase.php');

	// Define error codes for all Connector services responses
define('T3_ERR_SV_CONNECTION_FAILED', -50); // connection to remote server failed
define('T3_ERR_SV_BAD_RESPONSE', -51); // returned response is malformed or somehow unexpected
define('T3_ERR_SV_DISTANT_ERROR', -52); // returned response contains an error message

/**
 * The "Connector Services" family of services is designed to handle connections
 * to external servers and pass request to and from those servers, using whatever
 * protocols are appropriate
 *
 * This class is a base class for all Connector Services. It should be inherited
 * by all specific Connector Services implementations. This class should not be called
 * directly as it is unable to do anything by itself.
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 */
// TODO: move tx_svconnector_sv1 to tx_svconnector_base, provide wrapper tx_svconnector_sv1 for backwards compatibility
abstract class tx_svconnector_sv1 extends t3lib_svbase {
	protected $extKey = 'svconnector'; // The extension key
	protected $parentExtKey = 'svconnector'; // A copy of the extension key so that it is not overridden by children classes
	protected $extConfiguration; // The extension configuration
	protected $lang; // Language object
	
	/**
	 * Verifies that the connection is functional
	 * Returns false if not. This base implementation always returns false,
	 * since it is not supposed to be called directly
	 *
	 * @return	boolean		TRUE if the service is available
	 */
	public function init() {
		$this->extConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->parentExtKey]);
		if (isset($GLOBALS['LANG'])) {
			$this->lang = $GLOBALS['LANG'];
		}
		elseif (isset($GLOBALS['TSFE']->lang)) {
			$this->lang = $GLOBALS['TSFE']->lang;
		}
		return FALSE;
	}

	/**
	 * This method calls the query and returns the results from the response as is
	 * It also implements the processRawData hook for processing the results returned by the distant source
	 *
	 * @param	array	$parameters: parameters for the call
	 *
	 * @return	mixed	server response
	 */
	public function fetchRaw($parameters) {
		$result = $this->query();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['processRaw'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['processRaw'] as $className) {
				$processor = &t3lib_div::getUserObj($className);
				$result = $processor->processRaw($result, $this);
			}
		}
		return $result;
	}

	/**
	 * This method calls the query and returns the results from the response as an XML structure
	 *
	 * @param	array	$parameters: parameters for the call
	 *
	 * @return	string	XML structure
	 */
	public function fetchXML($parameters) {
		$result = $this->query($parameters);
		// Transform result to XML (if necessary) and return it
		// Implement processXML hook (see fetchRaw())
	}

	/**
	 * This method calls the query and returns the results from the response as a PHP array
	 *
	 * @param	array	$parameters: parameters for the call
	 *
	 * @return	array	PHP array
	 */
	public function fetchArray($parameters) {
		$result = $this->query($parameters);
		// Transform result to PHP array and return it
		// Implement processArray hook (see fetchRaw())
	}

	/**
	 * This method can be called to perform specific operations at some point after
	 * any of the fetch methods have been called. It does nothing by itself,
	 * but provides a hook for custom post-processing
	 *
	 * @param	array	$parameters: parameters for the call
	 * @param	mixed	$status: some form of status can be passed as argument
	 *					The nature of that status will depend on which process is calling this method
	 */
	public function postProcessOperations($parameters, $status) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['postProcessOperations'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['postProcessOperations'] as $className) {
				$processor = &t3lib_div::getUserObj($className);
				$processor->postProcessOperations($parameters, $status, $this);
			}
		}
	}

	/**
	 * This method queries the distant server given some parameters and returns the server response
	 * This base implementation just shows how to use the processParameters. It calls on the functions using the hook
	 * if they are any or else assembles a simple, HTTP-like query string.
	 * It also calls a hook for processing the raw data after getting it from the distant source
	 * This is just an example and you will need to implement your own query() method.
	 *
	 * @param	array	$parameters: parameters for the call
	 *
	 * @return	mixed	server response
	 */
	protected function query($parameters) {
		$queryString = '';
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['processParameters'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['processParameters'] as $className) {
				$processor = &t3lib_div::getUserObj($className);
				$queryString = $processor->processParameters($parameters, $this);
			}
		}
		elseif (is_array($parameters)) {
			foreach ($parameters as $key => $value) {
				$cleanValue = trim($value);
				$queryString .= '&'.$key.'='.urlencode($cleanValue);
			}
		}
		// Query the distant source and get the result
		// Include any necessary error processing
		// Process the result if any hook is registered
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['processResponse'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extKey]['processResponse'] as $className) {
				$processor = &t3lib_div::getUserObj($className);
				$result = $processor->processResponse($result, $this);
			}
		}
		// Return the result
	}

	/**
	 * This method should be used by all connector services when they encounter a fatal error
	 * It will write the error in the devlog (if activated) and throw an exception
	 *
	 * @param	string		$message: error message
	 * @param	integer		$exceptionNumber: number of the exception
	 * @param	array		$extraData: additional data to be passed to the devlog
	 */
	protected function raiseError($message, $exceptionNumber, array $extraData) {
		if (!empty($this->extConfiguration['debug'])) {
			t3lib_div::devLog($message, $this->extKey, 3, $extraData);
		}
		throw new Exception($message, $exceptionNumber);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svconnector/sv1/class.tx_svconnector_sv1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svconnector/sv1/class.tx_svconnector_sv1.php']);
}
?>