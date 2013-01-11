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
 * Controller for the backend module
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */
class Tx_Svconnector_Controller_TestingController extends Tx_Extbase_MVC_Controller_ActionController {
	/**
	 * @var Tx_Svconnector_Domain_Repository_ConnectorRepository
	 */
	protected $connectorRepository;

	/**
	 * List of configuration samples provided by the various connector services
	 * @var array
	 */
	protected $sampleConfigurations = array();

	/**
	 * Injects an instance of the connector repository
	 *
	 * @param Tx_Svconnector_Domain_Repository_ConnectorRepository $connectorRepository
	 * @return void
	 */
	public function injectConfigurationRepository(Tx_Svconnector_Domain_Repository_ConnectorRepository $connectorRepository) {
		$this->connectorRepository = $connectorRepository;
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @param Tx_Extbase_MVC_View_ViewInterface $view The view to be initialized
	 * @return void
	 * @api
	 */
	protected function initializeView(Tx_Extbase_MVC_View_ViewInterface $view) {
			// Get the sample configurations provided by the various connector services
		$this->sampleConfigurations = $this->connectorRepository->findAllSampleConfigurations();
		$view->assign('samples', $this->sampleConfigurations);
	}

	/**
	 * Renders the form for testing services
	 *
	 * @return void
	 */
	public function defaultAction() {
			// Check unavailable services
			// If there are any, display a warning about it
		$unavailableServices = $this->connectorRepository->findAllUnavailable();
		if (count($unavailableServices) > 0) {
				/** @var $messageObject t3lib_FlashMessage */
			$messageObject = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				Tx_Extbase_Utility_Localization::translate(
					'services.not.available',
					'svconnector',
					array(implode(', ', $unavailableServices))
				),
				'',
				t3lib_FlashMessage::WARNING
			);
			t3lib_FlashMessageQueue::addMessage($messageObject);
		}
			// Get available services and pass them to the view
		$availableServices = $this->connectorRepository->findAllAvailable();
		$this->view->assign('services', $availableServices);
		if (count($availableServices) == 0) {
				// If there are no available services, but some are not available, it means all installed connector
				// services are unavailable. This is a weird situation, we issue a warning.
			if (count($unavailableServices) > 0) {
					/** @var $messageObject t3lib_FlashMessage */
				$messageObject = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					Tx_Extbase_Utility_Localization::translate('no.services.available', 'svconnector'),
					'',
					t3lib_FlashMessage::WARNING
				);

				// If there are simply no services, we display a notice
			} else {
					/** @var $messageObject t3lib_FlashMessage */
				$messageObject = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					Tx_Extbase_Utility_Localization::translate('no.services', 'svconnector'),
					'',
					t3lib_FlashMessage::NOTICE
				);
			}
			t3lib_FlashMessageQueue::addMessage($messageObject);
		}

			// Check if a request for testing was submitted
			// If yes, execute the testing and pass both arguments and result to the view
		if ($this->request->hasArgument('tx_svconnector')) {
			$arguments = $this->request->getArgument('tx_svconnector');
			$this->view->assign('selectedService', $arguments['service']);
				// If no parameters were passed, try to fall back on sample configuration, if defined
			if (empty($arguments['parameters'])) {
				$parameters = (isset($this->sampleConfigurations[$arguments['service']])) ? $this->sampleConfigurations[$arguments['service']] : '';
			} else {
				$parameters = $arguments['parameters'];
			}
			$this->view->assign('parameters', $parameters);
			$this->view->assign('format', $arguments['format']);
			$this->view->assign('testResult', $this->performTest($arguments['service'], $arguments['parameters'], $arguments['format']));
		} else {
				// Select the first service in the list as default and get its sample configuration, if defined
			$defaultService = key($availableServices);
			$defaultParameters = (isset($this->sampleConfigurations[$defaultService])) ? $this->sampleConfigurations[$defaultService] : '';
			$this->view->assign('selectedService', $defaultService);
			$this->view->assign('parameters', $defaultParameters);
			$this->view->assign('format', 0);
			$this->view->assign('testResult', '');
		}
	}

	/**
	 * Performs the connection test for the selected service and passes the appropriate results to the view
	 *
	 * @param string $service Key of the service to test
	 * @param string $parameters Parameters for the service being tested
	 * @param integer $format Type of format to use (0 = raw, 1 = array, 2 = xml)
	 * @return string Result from the test
	 */
	protected function performTest($service, $parameters, $format) {
		$result = '';

			// Get the corresponding service object from the repository
		$serviceObject = $this->connectorRepository->findServiceByKey($service);
		if ($serviceObject->init()) {
			$parameters = $this->parseParameters($parameters);
			try {
					// Call the right "fetcher" depending on chosen format
				switch ($format) {
					case 1:
						$result = $serviceObject->fetchArray($parameters);
						break;
					case 2:
						$result = $serviceObject->fetchXML($parameters);
						break;
					default:
						$result = $serviceObject->fetchRaw($parameters);
						break;
				}
					// If the result is empty, issue an information message
				if (empty($result)) {
						/** @var $messageObject t3lib_FlashMessage */
					$messageObject = t3lib_div::makeInstance(
						't3lib_FlashMessage',
						Tx_Extbase_Utility_Localization::translate('no.result', 'svconnector'),
						'',
						t3lib_FlashMessage::INFO
					);
					t3lib_FlashMessageQueue::addMessage($messageObject);
				}
			}
				// Catch the exception and display an error message
			catch (Exception $e) {
					/** @var $messageObject t3lib_FlashMessage */
				$messageObject = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					Tx_Extbase_Utility_Localization::translate('service.error', 'svconnector', array($e->getMessage(), $e->getCode())),
					'',
					t3lib_FlashMessage::ERROR
				);
				t3lib_FlashMessageQueue::addMessage($messageObject);
			}
		}
		return $result;
	}

	/**
	 * Parses the parameters input string and transforms it into an array of key-value pairs
	 *
	 * @param string $parametersString Input string from the query variables
	 * @return array Array of key-value pairs
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
}
?>