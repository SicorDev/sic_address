<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "sic_address".
 *
 * Auto generated 27-03-2020 09:53
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['sic_address'] = [
    'title' => 'Address Listing',
    'description' => 'Address Extension that can either enhance or replace tt_address. You can add required fields dynamically like mask/powermail. Originally it was written in 2016 to replace extensions like nicos_directory, wt_directory or sp_directory and grew from there.',
    'category' => 'plugin',
    'author' => 'SICOR DEVTEAM',
    'author_email' => 'dev@sicor-kdl.net',
    'state' => 'beta',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-10.4.99',
            'php' => '7.0.0-7.4.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'tt_address' => ''
        ],
    ],
    'clearcacheonload' => false,
    'author_company' => 'SICOR',
];
