<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "svconnector".
 *
 * Auto generated 24-01-2013 15:17
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

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
	'module' => 'mod1',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '2.2.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.0.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'devlog' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:24:{s:9:"ChangeLog";s:4:"700d";s:29:"class.tx_svconnector_base.php";s:4:"3793";s:32:"class.tx_svconnector_utility.php";s:4:"4b63";s:16:"ext_autoload.php";s:4:"7923";s:21:"ext_conf_template.txt";s:4:"f88e";s:12:"ext_icon.gif";s:4:"b262";s:17:"ext_localconf.php";s:4:"a5bb";s:14:"ext_tables.php";s:4:"9ad0";s:13:"locallang.xml";s:4:"b81b";s:10:"README.txt";s:4:"4d6d";s:40:"Classes/Controller/TestingController.php";s:4:"abe4";s:49:"Classes/Domain/Repository/ConnectorRepository.php";s:4:"f2e5";s:46:"Classes/ViewHelpers/Be/ContainerViewHelper.php";s:4:"5fb2";s:43:"Classes/ViewHelpers/Be/ResultViewHelper.php";s:4:"56b3";s:40:"Resources/Private/Language/locallang.xml";s:4:"93da";s:37:"Resources/Private/Layouts/Module.html";s:4:"aa88";s:48:"Resources/Private/Templates/Testing/Default.html";s:4:"e722";s:38:"Resources/Public/Images/moduleIcon.png";s:4:"95d9";s:37:"Resources/Public/JavaScript/Module.js";s:4:"c9d5";s:14:"doc/manual.pdf";s:4:"1c1f";s:14:"doc/manual.sxw";s:4:"2b9e";s:18:"mod1/locallang.xml";s:4:"174b";s:22:"mod1/locallang_mod.xml";s:4:"f1b7";s:32:"sv1/class.tx_svconnector_sv1.php";s:4:"bdf3";}',
	'suggests' => array(
	),
);

?>