<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add module to the BE
	// Avoid loading the module when in the frontend or the Install Tool
if (TYPO3_MODE == 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
		// Register the backend module
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'tools', // Make module a submodule of 'tools' (admin)
		'svconnector', // Submodule key
		'', // Position
		array(
				// An array holding the controller-action-combinations that are accessible
			'Testing' => 'default'
		),
		array(
			'access' => 'admin',
			'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/moduleIcon.png',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xml'
		)
	);
}
?>