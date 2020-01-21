<?php // Auto generated on '{now}'! Do NOT edit !!!

return array(
    'ctrl' => array(
        'title'	=> 'LLL:EXT:sic_address/Resources/Private/Language/locallang.xlf:tx_sicaddress_domain_model_address_title',
        'label' => '{headline}',
        'label_userFunc' => "SICOR\\SicAddress\\Userfuncs\\Tca->addressTitle",
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        <f:if condition="{settings.customOrdering}">
        <f:then>
        'sortby' => 'sorting',
        </f:then>
        <f:else>
        'default_sortby' => '{orderbyquery}',
        </f:else>
        </f:if>
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => '<f:for each="{properties}" as="field">{field.title}, </f:for>',
        'iconfile' => 'EXT:sic_address/Resources/Public/Icons/tx_sicaddress_domain_model_address.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, categories,<f:for each="{properties}" as="field"> {field.title},</f:for>',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;1, categories,<f:for each="{properties}" as="field"> {field.title},</f:for> --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
                'default' => 0
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
    <f:for each="{properties}" as="field">
        <f:format.htmlentitiesDecode>
    '{field.title}' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.{field.title}',
        'config' => {field.config}
    ),
        </f:format.htmlentitiesDecode>
    </f:for>
    ),
);
