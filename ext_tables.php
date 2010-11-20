<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add module to the BE
if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txsvconnectorM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	t3lib_extMgm::addModule('tools', 'txsvconnectorM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}
?>