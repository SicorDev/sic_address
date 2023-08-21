<?php

defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('SicAddress', 'Sicaddress',
    [\SICOR\SicAddress\Controller\AddressController::class => 'list, show, new, create, edit, update, delete, search'],
    [\SICOR\SicAddress\Controller\AddressController::class => 'create, update, delete, search']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('SicAddress', 'SicaddressVianovisExport',
    [\SICOR\SicAddress\Controller\ExportController::class => 'exportVianovis'],
    []
);
