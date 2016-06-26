<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'SICOR.' . $_EXTKEY,
	'Sicaddress',
	'Adressverwaltung'
);

$extensionManagerSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);

if (TYPO3_MODE === 'BE' && $extensionManagerSettings["developerMode"]) {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'SICOR.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'sicaddress',	// Submodule key
		'',						// Position
		array(
			'Module' => 'list, create',
			'Import' => 'migrateNicosDirectory',
			'DomainProperty' => 'create, update, delete',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module_icon.png',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_sicaddress.xlf',
		)
	);
 
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'sic_address');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sicaddress_domain_model_address', 'EXT:sic_address/Resources/Private/Language/locallang_csh_tx_sicaddress_domain_model_address.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sicaddress_domain_model_address');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sicaddress_domain_model_domainproperty', 'EXT:sic_address/Resources/Private/Language/locallang_csh_tx_sicaddress_domain_model_domainproperty.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sicaddress_domain_model_domainproperty');;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    $_EXTKEY,
    'tx_sicaddress_domain_model_address'
);
