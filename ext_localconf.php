<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'SICOR.' . $_EXTKEY,
	'Sicaddress',
	array(
		'Address' => 'list, show, new, create, edit, update, delete, search',
		'Category' => 'list, show, new, create, edit, update, delete',
	),
	// non-cacheable actions
	array(
		'Address' => 'create, update, delete, search',
		'Category' => 'create, update, delete',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'SICOR.' . $_EXTKEY,
	'SicaddressVianovisExport',
	array(
		'Export' => 'exportVianovis',
	),
	// non-cacheable actions
	array(
	)
);

$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');

$signalSlotDispatcher->connect(
	'TYPO3\\CMS\\Extensionmanager\\Service\\ExtensionManagementService',
	'hasInstalledExtensions',
	'SICOR\\SicAddress\\Domain\\Service\\SignalService',
	'afterExtensionInstall',
	false
);