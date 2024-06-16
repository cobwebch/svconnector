<?php

declare(strict_types=1);

namespace Cobweb\Svconnector\Registry;

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

use Cobweb\Svconnector\Exception\UnknownServiceException;
use Cobweb\Svconnector\Service\ConnectorBase;

/**
 * Registry for the Connector family of services
 */
class ConnectorRegistry
{
    private array $connectors = [];

    public function __construct(iterable $connectors)
    {
        foreach ($connectors as $connector) {
            if (!($connector instanceof ConnectorBase)) {
                continue;
            }
            $type = $connector->getType();
            if ($type === '') {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Type for connector %s is empty.',
                        $connector::class
                    ),
                    1671361235
                );
            }
            if (isset($this->connectors[$type])) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Connector type %s is already registered.',
                        $type
                    ),
                    1671361286
                );
            }
            $this->connectors[$type] = $connector;
        }
    }

    /**
     * Returns all registered connector services
     *
     * @return iterable
     */
    public function getAllServices(): iterable
    {
        return $this->connectors;
    }

    /**
     * Returns a connector service object for the requested type
     *
     * @param string $type Type of connector service
     * @return ConnectorBase
     * @throws UnknownServiceException
     */
    public function getServiceForType(string $type): ConnectorBase
    {
        if (isset($this->connectors[$type])) {
            return $this->connectors[$type];
        }
        throw new UnknownServiceException(
            sprintf(
                'No connector service found for type %s',
                $type
            ),
            1671361439
        );
    }
}
