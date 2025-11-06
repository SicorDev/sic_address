<?php

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty',
        'label' => 'title',
        'hideTable' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'dividers2tabs' => TRUE,
        'sortby' => 'sorting',
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'searchFields' => 'title,tca_label,tca_override,settings,',
        'iconfile' => 'EXT:sic_address/Resources/Public/Icons/tx_sicaddress_domain_model_domainproperty.gif'
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;1, title, tca_label, tca_override, settings, is_list_label, type'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_sicaddress_domain_model_domainproperty',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
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
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'tca_label' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.tca_label',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'tca_override' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.tca_override',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            )
        ),
        'settings' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.settings',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            )
        ),
        'is_list_label' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.is_list_label',
            'config' => array(
                'type' => 'check',
                'default' => 0
            )
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.type',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_sicaddress_domain_model_fieldtype',
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'external' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_domainproperty.external',
            'config' => array(
                'type' => 'check',
                'default' => 0
            )
        ),
        'sorting' => array(
            'exclude' => 1,
            'config' => array(
                'type' => 'passthrough',
            ),

        )
    ),
);
