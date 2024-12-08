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

namespace Cobweb\Svconnector\Registry;

use Cobweb\Svconnector\Attribute\AsConnectorService;
use Cobweb\Svconnector\Exception\UnavailableServiceException;
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
            $type = null;
            // Get type and name from connector service attribute
            $reflection = new \ReflectionClass($connector::class);
            $attributes = $reflection->getAttributes(AsConnectorService::class);
            foreach ($attributes as $attribute) {
                $type = $attribute->getArguments()['type'];
                $name = $attribute->getArguments()['name'];
                $connector->setType($type);
                $connector->setName($name);
            }
            if (!($connector instanceof ConnectorBase)) {
                continue;
            }
            // If type has not been defined by attribute arguments, try to get it from class itself
            if ($type === null) {
                $type = $connector->getType();
            }
            if (empty($type)) {
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
            $connector->initialize();
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
     * @param array $parameters Parameters for the connector
     * @return ConnectorBase
     * @throws UnknownServiceException
     * @throws UnavailableServiceException
     */
    public function getServiceForType(string $type, array $parameters = []): ConnectorBase
    {
        if (isset($this->connectors[$type])) {
            /** @var ConnectorBase $connector */
            $connector = $this->connectors[$type];
            if (!$connector->isAvailable()) {
                throw new UnavailableServiceException(
                    sprintf(
                        'Connector service for type %s is not available.',
                        $type
                    ),
                    1733675709
                );
            }
            $connector->setParameters($parameters);
            return $connector;
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
