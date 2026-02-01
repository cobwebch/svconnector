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

namespace Cobweb\Svconnector\Event;

use Cobweb\Svconnector\Service\ConnectorServiceInterface;

/**
 * Event for processing the raw response received by the connector service when retrieving data.
 * Primarily meant for use in the query() method.
 */
final class ProcessResponseEvent
{
    public function __construct(protected mixed $response, protected ConnectorServiceInterface $connectorService) {}

    public function getConnectorService(): ConnectorServiceInterface
    {
        return $this->connectorService;
    }

    public function getResponse(): mixed
    {
        return $this->response;
    }

    public function setResponse(mixed $response): void
    {
        $this->response = $response;
    }
}
