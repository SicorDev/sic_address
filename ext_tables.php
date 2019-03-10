<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\GeneralUtility;

// Register Sicor icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon('extensions-sicor-icon', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', [
    'source' => 'EXT:sic_address/Resources/Public/Icons/module_icon_24.png',
]);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'SICOR.' . $_EXTKEY,
    'Sicaddress',
    'Adressverwaltung'
);

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY));
$pluginName = strtolower('sicaddress');
$pluginSignature = $extensionName . '_' . $pluginName;
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_sicaddresslist.xml');

$extensionManagerSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);
if (TYPO3_MODE === 'BE' && $extensionManagerSettings["developerMode"]) {
    // Registers Backend Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'SICOR.' . $_EXTKEY,
        'web',     // Make module a submodule of 'web'
        'sicaddress',    // Submodule key
        '',                        // Position
        array(
            'Module' => 'list, create, deleteFieldDefinitions',
            'Import' => 'migrateNicosDirectory, migrateSPDirectory, migrateOBG, migrateBezugsquelle, importTTAddress',
            'DomainProperty' => 'create, update, delete, sort',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module_icon_24.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_sicaddress.xlf',
        )
    );
}

if (TYPO3_MODE === 'BE' && $extensionManagerSettings["addressExport"]) {
    // Registers Backend Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'SICOR.' . $_EXTKEY,
        'web',     // Make module a submodule of 'web'
        'sicaddressexport',    // Submodule key
        '',                        // Position
        array(
            'Export' => 'export, exportToFile',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module_icon_24.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_sicaddressexport.xlf',
        )
    );
}

if (TYPO3_MODE === 'BE' && $extensionManagerSettings["doublets"]) {
    // Registers Backend Doublets Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'SICOR.' . $_EXTKEY,
        'web',     // Make module a submodule of 'web'
        'sicaddressdoublets',    // Submodule key
        '',                        // Position
        array(
            'Module' => 'doublets,ajaxDoublets,ajaxDeleteDoublet,switchDatasets',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module_icon_24.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_sicaddress_doublets.xlf',
        )
    );
}

if ($extensionManagerSettings["ttAddressMapping"]) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\SICOR\SicAddress\Task\AddGeoLocationTask::class] = array(
        'extension' => $_EXTKEY,
        'title' => 'GeoLocation für tt_address',
        'description' => 'Geht durch alle vorhandenen Address Einträge und führt die Verortung durch.',
        'additionalFields' => NULL
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TSconfig/Page/wizard.txt">');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Adressverwaltung');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sicaddress_domain_model_domainproperty', 'EXT:sic_address/Resources/Private/Language/locallang_csh_tx_sicaddress_domain_model_domainproperty.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sicaddress_domain_model_domainproperty');
