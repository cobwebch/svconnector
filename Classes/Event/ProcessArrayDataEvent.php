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
 * Event for processing data in array format retrieved by connector services.
 * Primarily meant for use in the fetchArray() method.
 */
final class ProcessArrayDataEvent
{
    protected array $data;
    protected ConnectorServiceInterface $connectorService;

    public function __construct(array $data, ConnectorServiceInterface $connectorService)
    {
        $this->data = $data;
        $this->connectorService = $connectorService;
    }

    public function getConnectorService(): ConnectorServiceInterface
    {
        return $this->connectorService;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

}
