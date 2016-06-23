<?php

if (!isset($GLOBALS['TCA']['sys_category']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['sys_category']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['sys_category']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['sys_category']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumnstx_sicaddress_sys_category = array();
	$tempColumnstx_sicaddress_sys_category[$GLOBALS['TCA']['sys_category']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => array(
				array('Category','Tx_SicAddress_Category')
			),
			'default' => 'Tx_SicAddress_Category',
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $tempColumnstx_sicaddress_sys_category, 1);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'sys_category',
	$GLOBALS['TCA']['sys_category']['ctrl']['type'],
	'',
	'after:' . $GLOBALS['TCA']['sys_category']['ctrl']['label']
);

/* inherit and extend the show items from the parent class */

if(isset($GLOBALS['TCA']['sys_category']['types']['1']['showitem'])) {
	$GLOBALS['TCA']['sys_category']['types']['Tx_SicAddress_Category']['showitem'] = $GLOBALS['TCA']['sys_category']['types']['1']['showitem'];
} elseif(is_array($GLOBALS['TCA']['sys_category']['types'])) {
	// use first entry in types array
	$sys_category_type_definition = reset($GLOBALS['TCA']['sys_category']['types']);
	$GLOBALS['TCA']['sys_category']['types']['Tx_SicAddress_Category']['showitem'] = $sys_category_type_definition['showitem'];
} else {
	$GLOBALS['TCA']['sys_category']['types']['Tx_SicAddress_Category']['showitem'] = '';
}
$GLOBALS['TCA']['sys_category']['types']['Tx_SicAddress_Category']['showitem'] .= ',--div--;LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_category,';
$GLOBALS['TCA']['sys_category']['types']['Tx_SicAddress_Category']['showitem'] .= '';

$GLOBALS['TCA']['sys_category']['columns'][$GLOBALS['TCA']['sys_category']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:sys_category.tx_extbase_type.Tx_SicAddress_Category','Tx_SicAddress_Category');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'',
	'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);