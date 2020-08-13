<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SICOR.sic_address',
    'Sicaddress',
    array(
        'Address' => 'list, show, new, create, edit, update, delete, search',
        'Category' => 'list, show, new, create, edit, update, delete',
    ),
    // non-cacheable actions
    array(
        'Address' => 'create, update, delete, search, map',
        'Category' => 'create, update, delete',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SICOR.sic_address',
    'SicaddressVianovisExport',
    array(
        'Export' => 'exportVianovis',
    ),
    // non-cacheable actions
    array()
);

// Register used signals
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Extensionmanager\\Service\\ExtensionManagementService',
    'hasInstalledExtensions',
    'SICOR\\SicAddress\\Domain\\Service\\SignalService',
    'afterExtensionInstall',
    false
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Extensionmanager\\Controller\\ConfigurationController',
    'afterExtensionConfigurationWrite',
    'SICOR\\SicAddress\\Domain\\Service\\SignalService',
    'refreshModuleList',
    false
);
