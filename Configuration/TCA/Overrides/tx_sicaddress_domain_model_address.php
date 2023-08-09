<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

use SICOR\SicAddress\Utility\Service;

call_user_func(function($extensionKey, $table)
{
    if (!class_exists(Service::class)) {
        $path = ExtensionManagementUtility::extPath('sic_address');
        require_once($path . 'Classes/Utility/Service.php');
    }

    $extensionConfiguration = Service::getConfiguration();

    if (
        !$extensionConfiguration["ttAddressMapping"]
    ) {
        $GLOBALS['TCA'][$table]['columns']['categories'] = [
            'config' => [
                'type' => 'category',
                'foreign_table_where' => ' AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.title'
            ]
        ];
    }

    ExtensionManagementUtility::addToAllTCAtypes($table, 'categories');
    ExtensionManagementUtility::addLLrefForTCAdescr($table, 'EXT:' . $extensionKey . '/Resources/Private/Language/locallang_csh_' . $table . '.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages($table);
}, 'sic_address', basename(__FILE__, '.php'));


