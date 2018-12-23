<?php

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
$GLOBALS['TCA']['sys_category']['ctrl']['default_sortby'] = 'ORDER BY title';
$GLOBALS['TCA']['sys_category']['ctrl']['sortby'] = '';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tx_sicaddress_domain_model_address',
	'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);
