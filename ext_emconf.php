<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "sic_address".
 *
 * Auto generated 22-11-2021 13:12
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Address Listing',
  'description' => 'Address Extension that can either enhance or replace tt_address. You can add required fields dynamically like mask/powermail. Originally it was written in 2016 to replace extensions like nicos_directory, wt_directory or sp_directory and grew from there.',
  'category' => 'plugin',
  'author' => 'SICOR DEVTEAM',
  'author_email' => 'dev@sicor-kdl.net',
  'state' => 'stable',
  'uploadfolder' => false,
  'clearCacheOnLoad' => 0,
  'version' => '3.1.3',
  'constraints' =>
  array (
    'depends' =>
    array (
      'typo3' => '9.5.0-10.4.99',
      'php' => '7.0.0-7.4.99',
    ),
    'conflicts' =>
    array (
    ),
    'suggests' =>
    array (
      'tt_address' => '',
    ),
  ),
  'clearcacheonload' => false,
  'author_company' => 'SICOR',
);

