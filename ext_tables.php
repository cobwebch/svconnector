<?php

// Register the backend module
// TODO: remove when compatibility with TYPO3 11 is dropped
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Svconnector',
    // Make it a submodule of 'Admin Tools'
    'tools',
    // Submodule key
    'tx_Svconnector',
    // Position
    '',
    [
        \Cobweb\Svconnector\Controller\TestingController::class => 'default'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:svconnector/Resources/Public/Icons/ModuleSvconnector.svg',
        'labels' => 'LLL:EXT:svconnector/Resources/Private/Language/locallang.xlf'
    ]
);
