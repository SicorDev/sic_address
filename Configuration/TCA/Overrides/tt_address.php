
<?php

if (!isset($GLOBALS['TCA']['tt_address']['ctrl']['type'])) {
    if (file_exists($GLOBALS['TCA']['tt_address']['ctrl']['dynamicConfigFile'])) {
        require_once($GLOBALS['TCA']['tt_address']['ctrl']['dynamicConfigFile']);
    }
    // no type field defined, so we define it here. This will only happen the first time the extension is installed!!
    $GLOBALS['TCA']['tt_address']['ctrl']['type'] = 'tx_extbase_type';
    $tempColumnstx_sicaddress_tt_address = array();
    $tempColumnstx_sicaddress_tt_address[$GLOBALS['TCA']['tt_address']['ctrl']['type']] = array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress.tx_extbase_type',
        'config' => array(
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => array(
                array('Address','Tx_SicAddress_Address')
            ),
            'default' => 'Tx_SicAddress_Address',
            'size' => 1,
            'maxitems' => 1,
        )
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address', $tempColumnstx_sicaddress_tt_address, 1);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_address',
    $GLOBALS['TCA']['tt_address']['ctrl']['type'],
    '',
    'after:' . $GLOBALS['TCA']['tt_address']['ctrl']['label']
);

$tmp_sic_address_columns = array(
     
        
    'hans' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.hans',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
        '{property.fieldName}',
        array(
                'appearance' => array(
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
                ),
                'foreign_types' => array(
                        '0' => array(
                                'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
                                'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                                'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
                                'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
                                'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
                                'showitem' => '
                                --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        )
                )
        ),
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
),

    ),
        
    
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address',$tmp_sic_address_columns);

/* inherit and extend the show items from the parent class */

if(isset($GLOBALS['TCA']['tt_address']['types']['0']['showitem'])) {
    $GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] = $GLOBALS['TCA']['tt_address']['types']['0']['showitem'];
} elseif(is_array($GLOBALS['TCA']['tt_address']['types'])) {
    // use first entry in types array
    $tt_address_type_definition = reset($GLOBALS['TCA']['tt_address']['types']);
    $GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] = $tt_address_type_definition['showitem'];
} else {
    $GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] = '';
}
$GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] .= ',--div--;LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address,';
$GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] .= ' hans,';

$GLOBALS['TCA']['tt_address']['columns'][$GLOBALS['TCA']['tt_address']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tt_address.tx_extbase_type.Tx_SicAddress_Address','Tx_SicAddress_Address');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    '',
    'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);
