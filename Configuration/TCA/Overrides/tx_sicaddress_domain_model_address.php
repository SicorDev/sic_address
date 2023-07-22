<?php

use SICOR\SicAddress\Utility\Service;

call_user_func(function($extensionKey, $table)
{
    if (!class_exists(SICOR\SicAddress\Utility\Service::class)) {
        $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sic_address');
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

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table, 'categories');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr($table, 'EXT:' . $extensionKey . '/Resources/Private/Language/locallang_csh_' . $table . '.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages($table);
}, 'sic_address', basename(__FILE__, '.php'));


