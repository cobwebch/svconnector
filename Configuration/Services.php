<?php

declare(strict_types=1);

use Cobweb\Svconnector\Service\ConnectorBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(ConnectorBase::class)->addTag('connector.service');
};
