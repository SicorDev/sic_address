<?php
namespace SICOR\SicAddress\Userfuncs;
use SICOR\SicAddress\Domain\Model\Address;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 DEV Team <dev@sicor-kdl.net>, SICOR KDL GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class Tca {

    public function addressTitle(&$parameters, $parentObject) {
        $addressRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $domainPropertyRepository = $extbaseObjectManager->get('SICOR\\SicAddress\\Domain\\Repository\\DomainPropertyRepository');

        $domainProperties = $domainPropertyRepository->findAll();
        foreach($domainProperties as $key => $value) {
            if(is_array($value)) $value = $value[0];
            
            if($value->getIsListLabel()) {
                if($addressRecord) {
                    $title = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($addressRecord, lcfirst($value->getTitle()));
                    $parameters['title'] .= $title . " ";
                }
            }
        }

        // Default: A helpful message...
        if(empty($parameters['title'])) {
            $parameters['title'] = '[Please use sic_address backend module to choose fields for the label.]';
        }
    }
}
