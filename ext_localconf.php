<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

	// Register connector AJAX responder
$TYPO3_CONF_VARS['BE']['AJAX']['svconnector::query'] = 'typo3conf/ext/svconnector/class.tx_svconnector_ajax.php:tx_svconnector_Ajax->query';
?>