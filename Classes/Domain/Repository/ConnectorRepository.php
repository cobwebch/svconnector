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
 * Repository for collecting connector services
 *
 * NOTE: this is not a true repository like in Extbase, as it does not access any persistence layer
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class Tx_Svconnector_Domain_Repository_ConnectorRepository {
	/**
	 * @var array List of available services
	 */
	protected $availableServices = array();

	/**
	 * @var array List of services that are not available, can be used for reporting
	 */
	protected $unavailableServices = array();

	/**
	 * @var array List of available service objects
	 */
	protected $serviceObjects = array();

	public function __construct() {
			// Assemble list of all available services
		if (isset($GLOBALS['T3_SERVICES']['connector'])) {
			foreach ($GLOBALS['T3_SERVICES']['connector'] as $serviceKey => $serviceInfo) {
					/** @var $serviceObject tx_svconnector_base */
				$serviceObject = t3lib_div::makeInstance($serviceInfo['className']);
					// If the service is available, add it to the list
				if ($serviceObject->init()) {
					$this->availableServices[$serviceKey] = $serviceInfo['title'];
						// Keep the objects in a separate array
					$this->serviceObjects[$serviceKey] = $serviceObject;
				} else {
					$this->unavailableServices[$serviceKey] = $serviceInfo['title'];
				}
			}
		}
	}

	/**
	 * Returns the list of available services
	 *
	 * @return array
	 */
	public function findAllAvailable() {
		return $this->availableServices;
	}

	/**
	 * Returns the list of unavailable services
	 *
	 * @return array
	 */
	public function findAllUnavailable() {
		return $this->unavailableServices;
	}

	/**
	 * Returns the service object given a key, if it exists
	 *
	 * @param string $key The key of the service to return
	 * @return tx_svconnector_base A connector service object
	 * @throws Exception
	 */
	public function findServiceByKey($key) {
		if (isset($this->serviceObjects[$key])) {
			return $this->serviceObjects[$key];
		} else {
			throw new Exception('No service available for key: ' . $key, 1346422543);
		}
	}

	public function findAllSampleConfigurations() {
		$configurationSamples = array();
		foreach ($this->availableServices as $key => $title) {
			$extension = $GLOBALS['T3_SERVICES']['connector'][$key]['extKey'];
			$configurationFile = t3lib_extMgm::extPath($extension, 'Resources/Public/Samples/Configuration.txt');
			if (file_exists($configurationFile)) {
				$configurationSamples[$key] = file_get_contents($configurationFile);
			}
		}
		return $configurationSamples;
	}
}
?>