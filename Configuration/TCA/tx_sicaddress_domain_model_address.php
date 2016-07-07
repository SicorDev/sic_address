<?php

return array(
    'ctrl' => array(
        'title'	=> 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address',
        'label' => 'categories',
        'label_userFunc' => "SICOR\\SicAddress\\Userfuncs\\Tca->addressTitle",
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'sortby' => 'sorting',

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'categories,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('sic_address') . 'Resources/Public/Icons/tx_sicaddress_domain_model_address.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, categories, tx_siccomcal_event_location, tx_spdirectory_cat, tx_spdirectory_fegroup, tx_spdirectory_feuser, tx_rggooglemap_lng, tx_rggooglemap_lat, tx_rggooglemap_display, tx_rggooglemap_cat, tx_rggooglemap_tab, tx_rggooglemap_cat2, tx_rggooglemap_ce, tx_msitesymstylespre_style, tx_msitegis_firstname, tx_msitegis_gis_x, tx_msitegis_gis_y, tx_msitegis_zentroid, tx_msitegis_geometry, tx_msitegis_gis_q, tx_rggooglemap_q,',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, categories, tx_siccomcal_event_location, tx_spdirectory_cat, tx_spdirectory_fegroup, tx_spdirectory_feuser, tx_rggooglemap_lng, tx_rggooglemap_lat, tx_rggooglemap_display, tx_rggooglemap_cat, tx_rggooglemap_tab, tx_rggooglemap_cat2, tx_rggooglemap_ce, tx_msitesymstylespre_style, tx_msitegis_firstname, tx_msitegis_gis_x, tx_msitegis_gis_y, tx_msitegis_zentroid, tx_msitegis_geometry, tx_msitegis_gis_q, tx_rggooglemap_q, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(

        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
                ),
            ),
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_sicaddress_domain_model_address',
                'foreign_table_where' => 'AND tx_sicaddress_domain_model_address.pid=###CURRENT_PID### AND tx_sicaddress_domain_model_address.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),

        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'categories' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.categories',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'sys_category',
                'MM' => 'tx_sicaddress_address_category_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'wizards' => array(
                    '_PADDING' => 1,
                    '_VERTICAL' => 1,
                    'edit' => array(
                        'module' => array(
                            'name' => 'wizard_edit',
                        ),
                        'type' => 'popup',
                        'title' => 'Edit',
                        'icon' => 'edit2.gif',
                        'popup_onlyOpenIfSelected' => 1,
                        'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ),
                    'add' => Array(
                        'module' => array(
                            'name' => 'wizard_add',
                        ),
                        'type' => 'script',
                        'title' => 'Create new',
                        'icon' => 'add.gif',
                        'params' => array(
                            'table' => 'sys_category',
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'prepend'
                        ),
                    ),
                ),
            ),
        ),
    
        
    'tx_siccomcal_event_location' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_siccomcal_event_location',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_spdirectory_cat' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_spdirectory_cat',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_spdirectory_fegroup' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_spdirectory_fegroup',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_spdirectory_feuser' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_spdirectory_feuser',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_lng' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_lng',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_lat' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_lat',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_display' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_display',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_cat' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_cat',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_tab' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_tab',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_cat2' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_cat2',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_ce' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_ce',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitesymstylespre_style' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitesymstylespre_style',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitegis_firstname' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitegis_firstname',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitegis_gis_x' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitegis_gis_x',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitegis_gis_y' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitegis_gis_y',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitegis_zentroid' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitegis_zentroid',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitegis_geometry' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitegis_geometry',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_msitegis_gis_q' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_msitegis_gis_q',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
        
    'tx_rggooglemap_q' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.tx_rggooglemap_q',
        'config' =>             array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
    ),
        
    
    ),
);