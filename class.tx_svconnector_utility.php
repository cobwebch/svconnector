<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Francois Suter (Cobweb) <typo3@cobweb.ch>
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
 * Utility class for the Connector family of services
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class tx_svconnector_utility {
	/**
	 * This method takes a XML structure and transforms it into a PHP array
	 * This array is rather complicated as the method tries not to loose information from the XML structure
	 *
	 * Considering the following XML:
	 *
	 * <test>
	 *		<foo>
	 *			bar
	 *			<child rank="#1?>Junior</child>
	 *			<child rank="#2?>Baby</child>
	 *		</foo>
	 * </test>
	 *
	 * the resulting array will look like:
	 *
	 * array(
	 *		'foo' => array(
	 *			0 => array(
	 *				'value' => 'bar',
	 *				'children' => array(
	 *					'child' => (
	 *						0 => array(
	 *							'value' => 'Junior',
	 *							'children' => array(),
	 *							'attributes' => array(
	 *								'rank' => '#1'
	 *							)
	 *						),
	 *						1 => array(
	 *							'value' => 'Baby',
	 *							'children' => array(),
	 *							'attributes' => array(
	 *								'rank' => '#2'
	 *							)
	 *						)
	 *					)
	 *				),
	 *				'attributes' => array()
	 *			)
	 *		)
	 * );
	 *
	 * NOTE: this method was written because t3lib_div::xml2array() is much too keyed
	 * to TYPO3's specifics and produces weird or even outright wrong array structures.
	 * On the other hand the reverse conversion is fine with t3lib_div::array2xml_cs().
	 *
	 * @param	string	$string: XML to parse
	 * @return	array	PHP array
	 */
	static public function convertXmlToArray($string) {
		$phpArray = array();
			// If input string is empty, exit with exception
		if (empty($string)) {
			throw new Exception('XML string is empty!', 1294325109);
		}

			// Try loading the string into the Simple XML library
		$xmlObject = simplexml_load_string($string);
			// If the value returned is false, the XML could not be parsed
		if ($xmlObject === FALSE) {
			throw new Exception('XML string is invalid!', 1294325195);
		}

			// Tranform XML into a PHP array
		foreach ($xmlObject as $key => $value) {
			if (!isset($phpArray[$key])) {
				$phpArray[$key] = array();
			}
			$phpArray[$key][] = self::handleXmlNode($value);
		}

		return $phpArray;
	}

	/**
	 * This method converts a given XML node into a PHP array, preserving all
	 * the attribute and children information. Calls itself recursively on child nodes.
	 *
	 * @param	SimpleXMLElement	$node: XML node to transform
	 * @return	array				Transformed XML node and children
	 */
	static public function handleXmlNode(SimpleXMLElement $node) {
			// Initializations
		$nodeArray = array();
		$nodeArray['value'] = trim((string)$node);
		$nodeArray['children'] = array();
		$nodeArray['attributes'] = array();
			// Loop on all children, if any
		$children = $node->children();
		if (count($children) > 0) {
				// If there are child nodes, recursively transform them into arrays too
			foreach ($children as $key => $subNode) {
				if (!isset($nodeArray[$key])) {
					$nodeArray[$key] = array();
				}
				$nodeArray['children'][$key][] = self::handleXmlNode($subNode);
			}
		}
			// Handle attributes, if any
		$attributes = $node->attributes();
		if (count($attributes) > 0) {
			foreach ($attributes as $key => $value) {
				$nodeArray['attributes'][$key] = (string)$value;
			}
		}
		return $nodeArray;
	}

	/**
	 * Dump a PHP array to a HTML table
	 * (This is somewhat similar to t3lib_div::view_array() but with styling ;-)
	 *
	 * @param	array	$array: Array to display
	 * @return	string	HTML table assembled from array
	 */
	static public function dumpArray($array) {
		$table = '<table border="0" cellpadding="1" cellspacing="1" bgcolor="#8a8a8a">';
		foreach ($array as $key => $value) {
			$table .= '<tr class="bgColor4-20" valign="top">';
			$table .= '<td>' . $key . '</td>';
			$table .= '<td>';
			if (is_array($value)) {
				$table .= self::dumpArray($value);
			} else {
				$table .= $value;
			}
			$table .= '</td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		return $table;
	}
}
?>