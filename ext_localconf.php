<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addService($_EXTKEY,  'connector' /* sv type */,  'tx_svconnector_sv1' /* sv key */,
		array(

			'title' => 'Connector Services',
			'description' => 'This family of services is used to connect to external data sources and fetch data from them. This is just a base class which cannot be used by itself. Implementations are done for specific subtypes.',

			'subtype' => '',

			'available' => TRUE,
			'priority' => 50,
			'quality' => 50,

			'os' => '',
			'exec' => '',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv1/class.tx_svconnector_sv1.php',
			'className' => 'tx_svconnector_sv1',
		)
	);
?>