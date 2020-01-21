<?php // Auto generated on '{now}'! Do NOT edit !!!
if(!empty($GLOBALS['TCA']['tt_address'])) {

    // Add additional columns to tt_address TCA
    $tmp_sic_address_columns = array(
<f:spaceless>
     <f:for each="{properties}" as="field">
     <f:if condition="{field.external} == '0'">
     <f:format.htmlentitiesDecode>
    '{field.title}' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.{field.title}',
        <f:if condition="{field.type.title} == 'rich'">'defaultExtras' => 'richtext[*]',</f:if>
        'config' => {field.config}
    ),
     </f:format.htmlentitiesDecode>
     </f:if>
    </f:for>
</f:spaceless>
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address',$tmp_sic_address_columns);

    // Extend show items from tt_address TCA
    $GLOBALS['TCA']['tt_address']['types']['0']['showitem'] .= ',--div--;sic_address,';
    $GLOBALS['TCA']['tt_address']['types']['0']['showitem'] .= '<f:for each="{properties}" as="field"><f:if condition="{field.external} == '0'"> {field.title},</f:if></f:for>';

    // Upgrade Description to an RTE field
    $GLOBALS['TCA']['tt_address']['columns']['description']['defaultExtras'] = 'richtext[*]';
    $GLOBALS['TCA']['tt_address']['columns']['description']['config']['wizards'] = array(
        'RTE' => array(
            'icon' => 'wizard_rte2.gif',
            'notNewRecords' => 1,
            'RTEonly' => 1,
            'module' => array(
                'name' => 'wizard_rich_text_editor',
                'urlParameters' => array(
                    'mode' => 'wizard',
                    'act' => 'wizard_rte.php'
                )
            ),
            'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
            'type' => 'script'
        )
    );

    // Set company field as label, name and email as alternative label, ordering by company
    $GLOBALS['TCA']['tt_address']['ctrl']['label'] = 'company';
    $GLOBALS['TCA']['tt_address']['ctrl']['label_alt'] = 'name, email';

    <f:if condition="{settings.customOrdering}">
        <f:then>
    $GLOBALS['TCA']['tt_address']['ctrl']['sortby'] = 'sorting';
        </f:then>
        <f:else>
            <f:if condition="{orderbyquery}">
            <f:then>
    $GLOBALS['TCA']['tt_address']['ctrl']['default_sortby'] = '{orderbyquery}';
            </f:then>
            <f:else>
    $GLOBALS['TCA']['tt_address']['ctrl']['default_sortby'] = 'ORDER BY company';
            </f:else>
            </f:if>
        </f:else>
    </f:if>


    // Add company to backend search
    $GLOBALS['TCA']['tt_address']['ctrl']['searchFields'] .= ', company';

    // Fix tt_address bug: https://github.com/FriendsOfTYPO3/tt_address/issues/55
    $GLOBALS['TCA']['tt_address']['columns']['birthday']['config'] = array(
        'type' => 'input',
        'renderType' => 'inputDateTime',
        'eval' => 'date',
        'size' => '8',
        'default' => 0
    );

    // Link to sys_category
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable('sic_address', 'tt_address');

}
