<?php

use SICOR\SicAddress\Controller\DomainPropertyController;
use SICOR\SicAddress\Controller\ExportController;
use SICOR\SicAddress\Controller\ImportController;
use SICOR\SicAddress\Controller\ModuleController;

$availableModules = [];

$availableModules ['sicaddress'] = [
    'parent' => 'tools',
    'position' => 'bottom',
    'access' => 'user',
    'workspaces' => '*',
    'path' => '/module/tools/sicaddress',
    'labels' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddress.xlf',
    'extensionName' => 'SicAddress',
    'iconIdentifier' => 'extensions-sicor-icon',
    'controllerActions' => [
        ModuleController::class => ['list', 'create', 'deleteFieldDefinitions', 'importTTAddress'],
        DomainPropertyController::class => ['create', 'update', 'delete', 'sort']
    ]
];

$availableModules ['sicaddressexport'] = [
    'parent' => 'tools',
    'position' => 'bottom',
    'access' => 'user',
    'workspaces' => '*',
    'path' => '/module/tools/sicaddressexport',
    'labels' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddressexport.xlf',
    'extensionName' => 'SicAddress',
    'iconIdentifier' => 'extensions-sicor-icon',
    'controllerActions' => [
        ExportController::class => ['export', 'exportToFile']
    ]
];

$availableModules ['sicaddressimport'] = [
    'parent' => 'tools',
    'position' => 'bottom',
    'access' => 'user',
    'workspaces' => '*',
    'path' => '/module/tools/sicaddressimport',
    'labels' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddressimport.xlf',
    'extensionName' => 'SicAddress',
    'iconIdentifier' => 'extensions-sicor-icon',
    'controllerActions' => [
        ImportController::class => ['import', 'importFromFile']
    ]
];

$availableModules ['sicaddressdoublets'] = [
    'parent' => 'tools',
    'position' => 'bottom',
    'access' => 'user',
    'workspaces' => '*',
    'path' => '/module/tools/sicaddressdoublets',
    'labels' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddress_doublets.xlf',
    'extensionName' => 'SicAddress',
    'iconIdentifier' => 'extensions-sicor-icon',
    'controllerActions' => [
        ModuleController::class => ['doublets', 'ajaxDoublets', 'ajaxDeleteDoublet', 'switchDatasets']
    ]
];

return $availableModules;
