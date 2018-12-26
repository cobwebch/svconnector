<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add module to the BE
if (TYPO3_MODE === 'BE') {
	// Register the backend module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
			'Cobweb.Svconnector',
            // Make it a submodule of 'Admin Tools'
			'tools',
            // Submodule key
			'tx_Svconnector',
            // Position
			'',
			[
				// An array holding the controller-action-combinations that are accessible
				'Testing' => 'default'
			],
			[
					'access' => 'admin',
					'icon' => 'EXT:svconnector/Resources/Public/Icons/ModuleSvconnector.svg',
					'labels' => 'LLL:EXT:svconnector/Resources/Private/Language/locallang.xlf'
			]
	);
}