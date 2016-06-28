<?php
namespace SICOR\SicAddress\Controller;
use SICOR\SicAddress\Domain\Model\DomainProperty;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 SICOR DEVTEAM <dev@sicor-kdl.net>, Sicor KDL GmbH
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

/**
 * ImportController
 */
class ImportController extends ModuleController {

    /**
     * Migrate from NicosDirectory
     */
    public function migrateNicosDirectoryAction()
    {
        // Get config
        $pid = $this->extbaseFrameworkConfiguration['persistence']['storagePid'];

        // Persistance manager
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");

        // Clear database
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_domainproperty");
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_address");
        $GLOBALS['TYPO3_DB']->exec_DELETEquery("sys_category", "pid = ".$pid);

        // Move legacy category data to sys_category
        $categories = $GLOBALS['TYPO3_DB']->exec_SELECTquery('name as title, '.$pid.' as pid', 'tx_nicosdirectory_category', 'deleted = 0 AND hidden = 0', '');
        while ($category = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($categories)) {
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', $category);
        }

        // Retrieve legacy address data
        $adresses = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'pid, tstamp, crdate, cruser_id, name as company, street, city, tel, fax, email, www, CAST(image AS CHAR(10000)) as image, category',
            'tx_nicosdirectory_entry',
            'deleted = 0 AND hidden = 0',
            '');

        // Add required fields to DomainProperty table
        $address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses);

        foreach($address as $key => $value) {
           if($key == "pid" || $key == "tstamp" || $key == "crdate" || $key == "cruser_id" || $key == "category")
               continue;

            $domainProperty = new \SICOR\SicAddress\Domain\Model\DomainProperty();
            $domainProperty->setProperties($key, "string", $key, "", "", "", false);
            $this->domainPropertyRepository->add($domainProperty);
        }

        // Enhance everything for new fields (sql, model, tca, ...)
        $persistenceManager->persistAll();
        $this->initializeAction();
        $this->createAction();

        do
        {
            // Insert address data into sic_address
            $sicAddress = $this->getSicAddrfromLegacyAddr($address);
            $this->addressRepository->add($sicAddress);
            $persistenceManager->persistAll();

            // Insert entry in sys_category_mm
            $foreignId = $sicAddress->getUid();
            $uids = explode(",", $address["category"]);
            foreach($uids as $uid) {
                $title = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('name', 'tx_nicosdirectory_category', 'uid = '.$uid, ''))["name"];
                $localId = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'sys_category', 'title = \''.$title.'\'', '', '', 1))["uid"];
                $mapping = array("uid_local" => $localId, 'uid_foreign' => $foreignId, 'tablenames' => 'tx_sicaddress_domain_model_address', 'fieldname' => 'categories');
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category_record_mm', $mapping);
            }
        }
        while ($address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses));

        // Migrate images to FAL
        $wizard = new \SICOR\SicAddress\Utility\FALImageWizard();
        $wizard->migrateNicosImages();

        $this->redirect("list", "Module");
    }

    /**
     * Transform key/value array into sic_address entries
     *
     * @param  \SICOR\SicAddress\Domain\Model\Address $address
     * @return \SICOR\SicAddress\Domain\Model\Address
     */
    public function getSicAddrfromLegacyAddr($address)
    {
        $sicaddress = new \SICOR\SicAddress\Domain\Model\Address();
        foreach($address as $key => $value)
            \TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($sicaddress, $key, $value);
        return $sicaddress;
    }
}