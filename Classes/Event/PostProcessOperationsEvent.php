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
 * Event for performing any custom process after the connector has finished its work.
 * It depends on method \Cobweb\Svconnector\Service\ConnectorBase::postProcessOperations()
 * being called. It receives a status information, the nature of which depends on the
 * code calling the post-process operations.
 */
final class PostProcessOperationsEvent
{
    protected mixed $status;
    protected ConnectorServiceInterface $connectorService;

    public function __construct(mixed $data, ConnectorServiceInterface $connectorService)
    {
        $this->status = $data;
        $this->connectorService = $connectorService;
    }

    public function getConnectorService(): ConnectorServiceInterface
    {
        return $this->connectorService;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

}