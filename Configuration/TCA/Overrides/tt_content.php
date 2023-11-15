<?php
defined('TYPO3') or die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin('SicAddress', 'AddressNew', 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddress.xlf:new_plugin_name');

$pluginSignature = ExtensionUtility::registerPlugin('SicAddress', 'SicAddress', 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddress.xlf:list_plugin_name');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:sic_address/Configuration/FlexForms/flexform_sicaddresslist.xml');

$pluginSignature = ExtensionUtility::registerPlugin('SicAddress', 'AddressShow', 'LLL:EXT:sic_address/Resources/Private/Language/locallang_sicaddress.xlf:show_plugin_name');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:sic_address/Configuration/FlexForms/flexform_sicaddressshow.xml');
