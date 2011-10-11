<?php

########################################################################
# Extension Manager/Repository config file for ext "svconnector".
#
# Auto generated 08-03-2011 22:04
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
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
	'module' => 'mod1',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '2.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'devlog' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"3ee0";s:10:"README.txt";s:4:"4d6d";s:29:"class.tx_svconnector_base.php";s:4:"3578";s:32:"class.tx_svconnector_utility.php";s:4:"f490";s:16:"ext_autoload.php";s:4:"7923";s:21:"ext_conf_template.txt";s:4:"f88e";s:12:"ext_icon.gif";s:4:"b262";s:17:"ext_localconf.php";s:4:"a5bb";s:14:"ext_tables.php";s:4:"aeef";s:13:"locallang.xml";s:4:"b81b";s:14:"doc/manual.pdf";s:4:"9d17";s:14:"doc/manual.sxw";s:4:"c24d";s:13:"mod1/conf.php";s:4:"43b5";s:14:"mod1/index.php";s:4:"2164";s:18:"mod1/locallang.xml";s:4:"174b";s:22:"mod1/locallang_mod.xml";s:4:"f1b7";s:22:"mod1/mod_template.html";s:4:"22c4";s:19:"mod1/moduleicon.gif";s:4:"f36f";s:32:"sv1/class.tx_svconnector_sv1.php";s:4:"bdf3";}',
	'suggests' => array(
	),
);

?>