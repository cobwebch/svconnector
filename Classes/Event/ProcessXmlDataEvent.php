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
 * Event for processing data in XML format retrieved by connector services.
 * Primarily meant for use in the fetchXml() method.
 */
final class ProcessXmlDataEvent
{
    protected string $data;
    protected ConnectorServiceInterface $connectorService;

    public function __construct(string $data, ConnectorServiceInterface $connectorService)
    {
        $this->data = $data;
        $this->connectorService = $connectorService;
    }

    public function getConnectorService(): ConnectorServiceInterface
    {
        return $this->connectorService;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

}
