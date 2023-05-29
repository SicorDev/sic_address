<?php

defined('TYPO3') or die('Access denied.');

call_user_func(function($extensionKey, $extensionName)
{
    if (!class_exists(SICOR\SicAddress\Utility\Service::class)) {
        $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);
        require_once($path . 'Classes/Utility/Service.php');
    }

    // Register Sicor icon
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon('extensions-sicor-icon', \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class, [
        'source' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/module_icon_24.png',
    ]);

    $extensionConfiguration = \SICOR\SicAddress\Utility\Service::getConfiguration();
    if ($extensionConfiguration["developerMode"]) {
        // Registers Backend Module
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            $extensionName,
            'web',     // Make module a submodule of 'web'
            'sicaddress',    // Submodule key
            '',                        // Position
            [
                \SICOR\SicAddress\Controller\ModuleController::class => 'list, create, deleteFieldDefinitions',
                \SICOR\SicAddress\Controller\ImportController::class => 'migrateNicosDirectory, migrateSPDirectory, migrateOBG, migrateBezugsquelle, importTTAddress',
                \SICOR\SicAddress\Controller\DomainPropertyController::class => 'create, update, delete, sort',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/module_icon_24.png',
                'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_sicaddress.xlf',
            ]
        );
    }

    if ($extensionConfiguration["addressExport"]) {
        // Registers Backend Module
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            $extensionName,
            'web',     // Make module a submodule of 'web'
            'sicaddressexport',    // Submodule key
            '',                        // Position
            [
                \SICOR\SicAddress\Controller\ExportController::class => 'export, exportToFile',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/module_icon_24.png',
                'labels' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddressexport.xlf',
            ]
        );
    }

    if ($extensionConfiguration["addressImport"]) {
        // Registers Backend Module
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            $extensionName,
            'web',     // Make module a submodule of 'web'
            'sicaddressimport',    // Submodule key
            '',                        // Position
            [
                \SICOR\SicAddress\Controller\ImportController::class => 'import, importFromFile',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/module_icon_24.png',
                'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_sicaddressimport.xlf',
            ]
        );
    }

    if ($extensionConfiguration["doublets"]) {
        // Registers Backend Doublets Module
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            $extensionName,
            'web',     // Make module a submodule of 'web'
            'sicaddressdoublets',    // Submodule key
            '',                        // Position
            [
                \SICOR\SicAddress\Controller\ModuleController::class => 'doublets,ajaxDoublets,ajaxDeleteDoublet,switchDatasets',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/module_icon_24.png',
                'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_sicaddress_doublets.xlf',
            ]
        );
    }

    if ($extensionConfiguration["ttAddressMapping"]) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\SICOR\SicAddress\Task\AddGeoLocationTask::class] = [
            'extension' => $extensionKey,
            'title' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang.xlf:geolocation_task_title',
            'description' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang.xlf:geolocation_task_description',
            'additionalFields' => null
        ];
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extensionKey . '/Configuration/TSconfig/Page/wizard.typoscript">');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sicaddress_domain_model_domainproperty', 'EXT:' . $extensionKey . '/Resources/Private/Language/locallang_csh_tx_sicaddress_domain_model_domainproperty.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sicaddress_domain_model_domainproperty');
}, 'sic_address', 'SicAddress');
