<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SICOR.sic_address',
    'Sicaddress',
    [
        \SICOR\SicAddress\Controller\AddressController::class => 'list, show, new, create, edit, update, delete, search'
    ],
    [
        \SICOR\SicAddress\Controller\AddressController::class => 'create, update, delete, search'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SICOR.sic_address',
    'SicaddressVianovisExport',
    [
        \SICOR\SicAddress\Controller\ExportController::class => 'exportVianovis',
    ],
    []
);

// Register used signals
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Extensionmanager\Service\ExtensionManagementService::class,
    'hasInstalledExtensions',
    \SICOR\SicAddress\Domain\Service\SignalService::class,
    'afterExtensionInstall',
    false
);

$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Extensionmanager\\Controller\\ConfigurationController',
    'afterExtensionConfigurationWrite',
    \SICOR\SicAddress\Domain\Service\SignalService::class,
    'refreshModuleList',
    false
);
