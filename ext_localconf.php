<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'SICOR.' . $_EXTKEY,
	'Sicaddress',
	array(
		'Address' => 'list, show, new, create, edit, update, delete',
		'Category' => 'list, show, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Address' => 'create, update, delete',
		'Category' => 'create, update, delete',
		
	)
);
