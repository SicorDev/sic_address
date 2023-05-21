<?php
defined('TYPO3') or die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'sic_address',
    'Sicaddress',
    'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddress.xlf:mlang_tabs_tab'
);

$pluginSignature = 'sicaddress_sicaddress';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:sic_address/Configuration/FlexForms/flexform_sicaddresslist.xml');
