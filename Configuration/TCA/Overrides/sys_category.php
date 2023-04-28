<?php

$GLOBALS['TCA']['sys_category']['ctrl']['default_sortby'] = 'ORDER BY title';
$GLOBALS['TCA']['sys_category']['ctrl']['sortby'] = 'sorting';

$newSysCategoryColumns = array(
    'sic_address_marker' => array(
        'exclude' => true,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang.xlf:sys_category_marker_icon',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'sic_address_marker',
            array(
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true
                ],
                'foreign_match_fields' => [
                    'fieldname' => 'sic_address_marker',
                    'tablenames' => 'sys_category',
                    'table_local' => 'sys_file',
                ],
            ),
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        )
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $newSysCategoryColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_category', '--div--;sic_address, sic_address_marker');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sicaddress_domain_model_address', 'EXT:/Resources/Private/Language/locallang_csh_.xlf');
