<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "sic_address".
 *
 * Auto generated 08-03-2022 11:50
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Address Listing',
    'description' => 'Address Extension that can either enhance or replace tt_address. You can add required fields dynamically like mask/powermail. Originally it was written in 2016 to replace extensions like nicos_directory, wt_directory or sp_directory and grew from there.',
    'category' => 'plugin',
    'version' => '5.0.3',
    'state' => 'stable',
    'uploadfolder' => false,
    'clearcacheonload' => false,
    'author' => 'SICOR DEVTEAM',
    'author_email' => 'dev@sicor-kdl.net',
    'author_company' => 'SICOR',
    'constraints' =>
        array(
            'depends' =>
                array(
                    'typo3' => '12.4.0-12.4.99',
                    'php' => '8.0.0-8.2.99',
                ),
            'suggests' =>
                array(
                    'tt_address' => '',
                ),
            'conflicts' =>
                array(),
        ),
);
