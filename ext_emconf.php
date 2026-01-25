<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Connector Services',
    'description' => 'This family of services is used to connect to external data sources and fetch data from them. This is just a base class which cannot be used by itself. Implementations are done for specific subtypes.',
    'category' => 'services',
    'author' => 'Francois Suter (Idéative)',
    'author_email' => 'typo3@ideative.ch',
    'state' => 'stable',
    'author_company' => '',
    'version' => '6.0.1',
    'constraints' =>
        [
            'depends' =>
                [
                    'php' => '8.2.0-8.5.99',
                    'typo3' => '13.4.0-14.4.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ],
];

