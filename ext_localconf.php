<?php

defined('TYPO3') or die('Access denied.');

call_user_func(function($extensionName)
{
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'Sicaddress',
        [
            \SICOR\SicAddress\Controller\AddressController::class => 'list, show, new, create, edit, update, delete, search'
        ],
        [
            \SICOR\SicAddress\Controller\AddressController::class => 'create, update, delete, search'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'SicaddressVianovisExport',
        [
            \SICOR\SicAddress\Controller\ExportController::class => 'exportVianovis',
        ],
        []
    );

    // Register used signals
//     $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
//     $signalSlotDispatcher->connect(
//         \TYPO3\CMS\Extensionmanager\Service\ExtensionManagementService::class,
//         'hasInstalledExtensions',
//         \SICOR\SicAddress\Domain\Service\SignalService::class,
//         'afterExtensionInstall',
//         false
//     );
// 
//     $signalSlotDispatcher->connect(
//         'TYPO3\\CMS\\Extensionmanager\\Controller\\ConfigurationController',
//         'afterExtensionConfigurationWrite',
//         \SICOR\SicAddress\Domain\Service\SignalService::class,
//         'refreshModuleList',
//         false
//     );
}, 'SicAddress');

