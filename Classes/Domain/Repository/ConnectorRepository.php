<?php
namespace Cobweb\Svconnector\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Cobweb\Svconnector\Service\ConnectorBase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for collecting connector services
 *
 * NOTE: this is not a true repository like in Extbase, as it does not access any persistence layer
 *
 * @author Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_svconnector
 */
class ConnectorRepository
{
    /**
     * @var array List of available services
     */
    protected $availableServices = [];

    /**
     * @var array List of services that are not available, can be used for reporting
     */
    protected $unavailableServices = [];

    /**
     * @var array List of available service objects
     */
    protected $serviceObjects = [];

    public function __construct()
    {
        // Assemble list of all available services
        if (isset($GLOBALS['T3_SERVICES']['connector'])) {
            foreach ($GLOBALS['T3_SERVICES']['connector'] as $serviceKey => $serviceInfo) {
                /** @var $serviceObject ConnectorBase */
                $serviceObject = GeneralUtility::makeInstance($serviceInfo['className']);
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
    public function findAllAvailable(): array
    {
        return $this->availableServices;
    }

    /**
     * Returns the list of unavailable services
     *
     * @return array
     */
    public function findAllUnavailable(): array
    {
        return $this->unavailableServices;
    }

    /**
     * Returns the service object given a key, if it exists
     *
     * @param string $key The key of the service to return
     * @return ConnectorBase A connector service object
     * @throws \Exception
     */
    public function findServiceByKey($key): ConnectorBase
    {
        if (isset($this->serviceObjects[$key])) {
            return $this->serviceObjects[$key];
        }
        throw new \Exception(
                'No service available for key: ' . $key,
                1346422543
        );
    }

    /**
     * Returns the list of all sample configurations.
     *
     * @return array
     */
    public function findAllSampleConfigurations(): array
    {
        $configurationSamples = [];
        foreach ($this->availableServices as $key => $title) {
            $extension = $GLOBALS['T3_SERVICES']['connector'][$key]['extKey'];
            $configurationFile = ExtensionManagementUtility::extPath(
                    $extension,
                    'Resources/Public/Samples/Configuration.txt'
            );
            if (file_exists($configurationFile)) {
                $configurationSamples[$key] = file_get_contents($configurationFile);
            }
        }
        return $configurationSamples;
    }
}
