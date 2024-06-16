<?php

use Cobweb\Svconnector\Controller\TestingController;
return [
    'Svconnector' => [
        'parent' => 'tools',
        'position' => ['after' => 'tools_toolsenvironment'],
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'tx_svconnector-test-module',
        'path' => '/module/tools/tx_svconnector',
        'labels' => 'LLL:EXT:svconnector/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'svconnector',
        'controllerActions' => [
            TestingController::class => [
                'default'
            ],
        ],
    ]
];
