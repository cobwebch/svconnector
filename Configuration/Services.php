<?php

declare(strict_types=1);

use Cobweb\Svconnector\Service\ConnectorServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(ConnectorServiceInterface::class)->addTag('connector.service');
};
