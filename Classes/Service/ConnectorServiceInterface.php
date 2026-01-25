<?php

declare(strict_types=1);

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

namespace Cobweb\Svconnector\Service;

use Cobweb\Svconnector\Domain\Model\Dto\CallContext;
use Cobweb\Svconnector\Domain\Model\Dto\ConnectionInformation;

/**
 * Defines the methods that represent the Connector Service API
 */
interface ConnectorServiceInterface
{
    /**
     * Returns the type of data handled by the connector service
     */
    public function getType(): string;

    /**
     * Sets the type of the connector service
     */
    public function setType(string $type): void;

    /**
     * Returns a descriptive name of the connector service
     */
    public function getName(): string;

    /**
     * Sets the name of the connector service
     */
    public function setName(string $name): void;

    /**
     * Performs any necessary initialization for the service. Called automatically by the connector registry.
     */
    public function initialize(): void;

    /**
     * Verifies that the connection is functional, returns false if not.
     *
     * @return bool TRUE if the service is available
     */
    public function isAvailable(): bool;

    /**
     * Set the connector parameters
     */
    public function setParameters(array $parameters): void;

    /**
     * Returns the sample configuration for the service, if any
     */
    public function getSampleConfiguration(): string;

    /**
     * Checks the connector configuration and returns notices, warnings or errors, if any.
     */
    public function checkConfiguration(): array;

    /**
     * Return the call context object
     */
    public function getCallContext(): CallContext;

    /**
     * Return the connection information object
     */
    public function getConnectionInformation(): ConnectionInformation;

    /**
     * Queries the source and returns the results from the response as is.
     *
     * It is recommended to fire the \Cobweb\Svconnector\Event\ProcessRawDataEvent in this method.
     *
     * @return mixed Server response
     */
    public function fetchRaw(): mixed;

    /**
     * Queries the source and returns the results from the response as an XML structure.
     *
     * You may rely on \TYPO3\CMS\Core\Utility\GeneralUtility::array2xml_cs() to convert an array response to XML.
     * It is recommended to fire the \Cobweb\Svconnector\Event\ProcessXmlDataEvent in this method.
     *
     * @return string XML structure
     */
    public function fetchXML(): string;

    /**
     * Queries the source and returns the results from the response as a PHP array.
     *
     * You may rely on \tx_svconnector_utility::convertXmlToArray() to convert
     * an XML response to an array.
     * It is recommended to fire the \Cobweb\Svconnector\Event\ProcessArrayDataEvent in this method.
     *
     * @return array PHP array
     */
    public function fetchArray(): array;

    /**
     * Performs post-process operations using events
     */
    public function postProcessOperations(mixed $status): void;
}
