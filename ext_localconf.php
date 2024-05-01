<?php
defined('TYPO3') or die('Access denied.');

use SICOR\SicAddress\Controller\AddressController;
use SICOR\SicAddress\Controller\ExportController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin('SicAddress', 'SicAddress', [AddressController::class => 'list, search'], [AddressController::class => 'search']);
ExtensionUtility::configurePlugin('SicAddress', 'AddressShow', [AddressController::class => 'show']);
ExtensionUtility::configurePlugin('SicAddress', 'AddressNew', [AddressController::class => 'new, create'], [AddressController::class => 'create']);

ExtensionUtility::configurePlugin('SicAddress', 'SicaddressVianovisExport', [ExportController::class => 'exportVianovis']);
