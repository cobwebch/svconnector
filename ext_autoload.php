<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id$
 */
$extensionPath = t3lib_extMgm::extPath('svconnector');
return array(
	'tx_svconnector_base' => $extensionPath . 'class.tx_svconnector_base.php',
	'tx_svconnector_utility' => $extensionPath . 'class.tx_svconnector_utility.php',
);
?>
