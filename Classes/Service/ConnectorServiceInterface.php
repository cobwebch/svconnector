<?php

declare(strict_types=1);

namespace Cobweb\Svconnector\Service;

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
     * Verifies that the connection is functional, returns false if not.
     *
     * @return boolean TRUE if the service is available
     */
    public function isAvailable(): bool;

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
     * @param mixed $status Some form of status can be passed as argument
     *                      The nature of that status will depend on which process is calling this method
     */
    public function postProcessOperations(array $parameters, $status);
}