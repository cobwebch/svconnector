<?php

########################################################################
# Extension Manager/Repository config file for ext: "svconnector"
#
# Auto generated 27-04-2010 17:52
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Connector Services',
	'description' => 'This family of services is used to connect to external data sources and fetch data from them. This is just a base class which cannot be used by itself. Implementations are done for specific subtypes.',
	'category' => 'services',
	'author' => 'Francois Suter (Cobweb)',
	'author_email' => 'typo3@cobweb.ch',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0'
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'devlog' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:7:{s:9:"ChangeLog";s:4:"e535";s:10:"README.txt";s:4:"4d6d";s:29:"class.tx_svconnector_base.php";s:4:"e603";s:21:"ext_conf_template.txt";s:4:"1a85";s:12:"ext_icon.gif";s:4:"74ea";s:14:"doc/manual.sxw";s:4:"9196";s:32:"sv1/class.tx_svconnector_sv1.php";s:4:"8855";}',
	'suggests' => array(
	),
);

?>