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
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Returns a descriptive name of the connector service
     *
     * @return string
     */
    public function getName(): string;

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
     *
     * @return string
     */
    public function getSampleConfiguration(): string;

    /**
     * Checks the connector configuration and returns notices, warnings or errors, if any.
     *
     * @param array $parameters Connector call parameters
     * @return array
     */
    public function checkConfiguration(array $parameters = []): array;

    /**
     * Return the call context object
     *
     * @return CallContext
     */
    public function getCallContext(): CallContext;

    /**
     * Return the connection information object
     *
     * @return ConnectionInformation
     */
    public function getConnectionInformation(): ConnectionInformation;

    /**
     * Calls the query and returns the results from the response as is.
     *
     * @param array $parameters Parameters for the call
     * @return mixed Server response
     */
    public function fetchRaw(array $parameters = []);

    /**
     * Calls the query and returns the results from the response as an XML structure.
     *
     * @param array $parameters Parameters for the call
     * @return string XML structure
     */
    public function fetchXML(array $parameters = []): string;

    /**
     * Calls the query and returns the results from the response as a PHP array.
     *
     * @param array $parameters Parameters for the call
     * @return array PHP array
     */
    public function fetchArray(array $parameters = []): array;

    /**
     * Performs post-process operations using events
     *
     * @param array $parameters Parameters for the call
     */
    public function postProcessOperations(array $parameters, mixed $status);
}
