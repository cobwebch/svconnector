<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "svconnector".
 *
 * Auto generated 05-04-2017 17:36
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
        'title' => 'Connector Services',
        'description' => 'This family of services is used to connect to external data sources and fetch data from them. This is just a base class which cannot be used by itself. Implementations are done for specific subtypes.',
        'category' => 'services',
        'author' => 'Francois Suter (Cobweb)',
        'author_email' => 'typo3@cobweb.ch',
        'state' => 'stable',
        'uploadfolder' => 0,
        'createDirs' => '',
        'clearCacheOnLoad' => 1,
        'author_company' => '',
        'version' => '3.3.1',
        'constraints' =>
                [
                        'depends' =>
                                [
                                        'typo3' => '8.7.0-9.99.99',
                                ],
                        'conflicts' =>
                                [
                                ],
                        'suggests' =>
                                [
                                ],
                ],
];

