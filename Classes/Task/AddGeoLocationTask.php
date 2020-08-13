<?php
namespace SICOR\SicAddress\Task;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 DEVTEAM <dev@sicor-kdl.net>, SICOR KDL
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use SICOR\SicAddress\Domain\Repository\AddressRepository;
use SICOR\SicAddress\Domain\Service\GeocodeService;

/**
 * AddGeoLocationTask
 */

class AddGeoLocationTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    public function execute()
    {
        // Check configuration
        $extensionManagerSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);
        if(!$extensionManagerSettings["ttAddressMapping"])
            return FALSE;
        
        // Prepare Address repository
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $addressRepository = $objectManager->get(AddressRepository::class);
        $querySettings = $objectManager->get(QuerySettingsInterface::class);
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setIgnoreEnableFields(true);
        $addressRepository->setDefaultQuerySettings($querySettings);

        // Create Geo service
        $geocodeService = GeneralUtility::makeInstance(GeocodeService::class);

        // Get records
        $records = $addressRepository->findGeoLessEntries();

        foreach ($records as $record)
        {
            // Build complete address string
            $address = $record->getAddress();
            $zip = $record->getZip();
            $city = $record->getCity();
            if (!empty($zip) || empty(!$city)) {
                $address .= ',';
                if (!empty($zip)) {
                    $address .= ' ' . $zip;
                }
                if (!empty($city)) {
                    $address .= ' ' . $city;
                }
            }

            // Update records
            $coordinates = $geocodeService->getCoordinatesForAddress(trim($address));
            if (is_array($coordinates) && !empty($coordinates['latitude']) && !empty($coordinates['longitude'])) {
                $record->setLatitude($coordinates['latitude']);
                $record->setLongitude($coordinates['longitude']);
            }
            else {
                $record->setLatitude(2.42);
                $record->setLongitude(2.42);
            }
            $addressRepository->update($record);
        }

        // Persist everything
        $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $persistenceManager->persistAll();

        return true;
    }
}
