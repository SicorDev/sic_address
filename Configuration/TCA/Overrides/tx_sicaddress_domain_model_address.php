<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sicaddress_domain_model_address', 'EXT:sic_address/Resources/Private/Language/locallang_csh_tx_sicaddress_domain_model_address.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sicaddress_domain_model_address');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable('sic_address', 'tx_sicaddress_domain_model_address');

if(isset($GLOBALS['TCA']['tx_sicaddress_domain_model_address']['columns']['categories'])) {
    $GLOBALS['TCA']['tx_sicaddress_domain_model_address']['columns']['categories']['config']['foreign_table_where'] = ' AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.title';
}
