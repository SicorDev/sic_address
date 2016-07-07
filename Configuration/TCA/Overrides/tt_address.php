
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
$GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] .= ' tx_siccomcal_event_location, tx_spdirectory_cat, tx_spdirectory_fegroup, tx_spdirectory_feuser, tx_rggooglemap_lng, tx_rggooglemap_lat, tx_rggooglemap_display, tx_rggooglemap_cat, tx_rggooglemap_tab, tx_rggooglemap_cat2, tx_rggooglemap_ce, tx_msitesymstylespre_style, tx_msitegis_firstname, tx_msitegis_gis_x, tx_msitegis_gis_y, tx_msitegis_zentroid, tx_msitegis_geometry, tx_msitegis_gis_q, tx_rggooglemap_q,';

$GLOBALS['TCA']['tt_address']['columns'][$GLOBALS['TCA']['tt_address']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tt_address.tx_extbase_type.Tx_SicAddress_Address','Tx_SicAddress_Address');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    '',
    'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);
