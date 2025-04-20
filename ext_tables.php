<?php
defined('TYPO3') or die('Access denied.');

// Register Sicor icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon('extensions-sicor-icon', \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class, [
    'source' => 'EXT:sic_address/Resources/Public/Icons/module_icon_24.png',
]);

$extensionConfiguration = \SICOR\SicAddress\Domain\Service\ConfigurationService::getConfiguration();
if ($extensionConfiguration["ttAddressMapping"]) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\SICOR\SicAddress\Task\AddGeoLocationTask::class] = array(
        'extension' => 'sic_address',
        'title' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang.xlf:geolocation_task_title',
        'description' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang.xlf:geolocation_task_description',
        'additionalFields' => NULL
    );
}

// Toggle Backend Module son/off according to extension config
if (!$extensionConfiguration["developerMode"]) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.hideModules := addToList(sicaddress)');
}
if (!$extensionConfiguration["addressExport"]) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.hideModules := addToList(sicaddressexport)');
}
if (!$extensionConfiguration["addressImport"]) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.hideModules := addToList(sicaddressimport)');
}
if (!$extensionConfiguration["doublets"]) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.hideModules := addToList(sicaddressdoublets)');
}

if ($extensionConfiguration["ttAddressMapping"]) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\SICOR\SicAddress\Task\AddGeoLocationTask::class] = array(
        'extension' => 'sic_address',
        'title' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang.xlf:geolocation_task_title',
        'description' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang.xlf:geolocation_task_description',
        'additionalFields' => NULL
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:sic_address/Configuration/TSconfig/Page/wizard.typoscript">');
