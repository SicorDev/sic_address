<?php

use SICOR\SicAddress\Controller\DomainPropertyController;
use SICOR\SicAddress\Controller\ExportController;
use SICOR\SicAddress\Controller\ImportController;
use SICOR\SicAddress\Controller\ModuleController;

$extensionKey = 'sic_address';
$extensionName = 'SicAddress';

/**
 * Definitions for modules provided by EXT:sic_address
 */
return [
    'sicaddress' => [
        'parent' => 'system',
        'position' => '',
        'access' => 'user,group',
        'workspaces' => '*',
        'path' => '/module/page/sicaddress',
        'labels' => 'LLL:EXT:' . $extensionKey. '/Resources/Private/Language/locallang_sicaddress.xlf',
        'extensionName' => $extensionName,
        'iconIdentifier' => 'module24',
        'controllerActions' => [
            ModuleController::class => [
                'list', 'create', 'deleteFieldDefinitions'
            ],
            ImportController::class => [
                'migrateNicosDirectory', 'migrateSPDirectory', 'migrateOBG', 'migrateBezugsquelle', 'importTTAddress'
            ],
            DomainPropertyController::class => [
                'create', 'update', 'delete', 'sort',
            ]
        ]
    ],
    'sicaddressexport' => [
        'parent' => 'system',
        'position' => '',
        'access' => 'user,group',
        'workspaces' => '*',
        'path' => '/module/page/sicaddressexport',
        'labels' => 'LLL:EXT:' . $extensionKey. '/Resources/Private/Language/locallang_sicaddressexport.xlf',
        'extensionName' => $extensionName,
        'iconIdentifier' => 'module24',
        'controllerActions' => [
            ExportController::class => [
                'export', 'exportToFile'
            ]
        ]
    ],
    'sicaddressimport' => [
        'parent' => 'system',
        'position' => '',
        'access' => 'user,group',
        'workspaces' => '*',
        'path' => '/module/page/sicaddressimport',
        'labels' => 'LLL:EXT:' . $extensionKey. '/Resources/Private/Language/locallang_sicaddressimport.xlf',
        'extensionName' => $extensionName,
        'iconIdentifier' => 'module24',
        'controllerActions' => [
            ImportController::class => [
                'import', 'importFromFile'
            ]
        ]
    ]
];
