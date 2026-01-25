<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

// TODO: remove old icon when dropping compatibility with TYPO3 13
$version = VersionNumberUtility::convertVersionStringToArray(VersionNumberUtility::getCurrentTypo3Version());
if ($version['version_main'] >= 14) {
    $source = 'EXT:svconnector/Resources/Public/Icons/module-svconnector.svg';
} else {
    $source = 'EXT:svconnector/Resources/Public/Icons/ModuleSvconnector.svg';
}
return [
    'tx_svconnector-test-module' => [
        'provider' => SvgIconProvider::class,
        'source' => $source,
    ],
];
