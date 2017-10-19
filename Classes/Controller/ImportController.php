<?php
namespace SICOR\SicAddress\Controller;
use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Model\DomainProperty;
use SICOR\SicAddress\Utility\FALImageWizard;
use SICOR\SicAddress\Utility\Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * Migrate from nicos_directory
     *
     * @return void
     */
    public function migrateNicosDirectoryAction()
    {
        // Get config
        $pid = $this->extbaseFrameworkConfiguration['persistence']['storagePid'];

        // Persistance manager
        $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");

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

            $domainProperty = new DomainProperty();
            $domainProperty->setProperties($key, "string", $key, "", "", false);
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
                $mapping = array("uid_local" => $localId,
                                 "uid_foreign" => $foreignId,
                                 "tablenames" => "tx_sicaddress_domain_model_address",
                                 "fieldname" => "categories");
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category_record_mm', $mapping);
            }
        }
        while ($address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses));

        // Migrate images to FAL
        $wizard = new FALImageWizard();
        $wizard->migrateNicosImages();

        $this->redirect("list", "Module");
    }

    /**
     * Migrate from sp_dir
     *
     * @return void
     */
    public function migrateSPDirectoryAction()
    {
        // Persistance manager
        $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");

        // Clear database
        $GLOBALS['TYPO3_DB']->exec_DELETEquery("sys_category_record_mm", "tablenames = 'tx_sicaddress_domain_model_address'");
        $GLOBALS['TYPO3_DB']->exec_DELETEquery("sys_category", "pid = 797");
        $maxuid = $GLOBALS['TYPO3_DB']->exec_SELECTquery('max(uid)', 'sys_category', '');
        $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE sys_category AUTO_INCREMENT = '.($maxuid+1));

        // Create Master category
        $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', array("title" => "Adressen", "pid" => 797));
        $persistenceManager->persistAll();
        $masterID=$GLOBALS['TYPO3_DB']->sql_insert_id();

        // Move legacy parent categories to sys_category
        $parents = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, catname as title, 797 as pid, '.$masterID.' as parent',
            'tx_spdirectory_cat', 'deleted = 0 AND hidden = 0 AND tx_msitespdhier_parent = 0');
        while ($category = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($parents))
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', $category);

        // Move legacy child categories to sys_category
        $childs = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, catname as title, 797 as pid, tx_msitespdhier_parent as parent',
            'tx_spdirectory_cat', 'deleted = 0 AND hidden = 0 AND tx_msitespdhier_parent > 0');
        while ($category = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($childs))
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', $category);

        // Move relations to sys_category_mm
        $mms = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local as uid_foreign, uid_foreign as uid_local, "tt_address" as tablenames, "categories" as fieldname, sorting', 'tx_spdirectory_cat_mm', '1=1');
        while ($mm = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($mms))
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category_record_mm', $mm);

        $this->redirect("list", "Module");
    }

    /**
     * Migrate OBG data from fe_user + sic_mm
     *
     * @return void
     */
    public function migrateOBGAction()
    {
        // Persistance manager
        $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");

        // Clear database
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_domainproperty");
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_address");
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("sys_category");

        // Move legacy category data to sys_category
        $categories = $GLOBALS['TYPO3_DB']->exec_SELECTquery('branche as title, pid', 'tx_sicmm_branche', 'deleted = 0 AND hidden = 0 AND ( pid = 52 OR pid = 391 )', '');
        while ($category = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($categories)) {
            $category['pid'] = ($category['pid'] == 52) ? 104 : 105;
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', $category);
        }

        // Retrieve legacy address data
        $adresses = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'feu.pid, company, street, tx_sicmm_street_number as housenumber, telephone, fax, email, zip, city, '.
            'www, tx_sicmm_company_logo as image, tx_sicmm_open_times as freetext, tx_sicmm_manager_mobile as mobile, tx_sicmm_company_manager as companymanager',
            'fe_users as feu, tx_sicmm_streets as str',
            'tx_sicmm_street = str.uid AND feu.deleted = 0 AND disable = 0 AND (feu.pid = 52 OR feu.pid = 391)',
            '');

        // Add required fields to DomainProperty table
        $address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses);

        foreach($address as $key => $value) {
            if($key == "pid")
                continue;

            $domainProperty = new DomainProperty();
            $domainProperty->setProperties($key, "string", $key, "", "", false);
            $this->domainPropertyRepository->add($domainProperty);
        }

        // Enhance everything for new fields (sql, model, tca, ...)
        $persistenceManager->persistAll();
        $this->initializeAction();
        $this->createAction();

        do
        {
            // Insert address data into sic_address
            $address['pid'] = ($address['pid'] == 52) ? 104 : 105;
            $sicAddress = $this->getSicAddrfromLegacyAddr($address);
            $this->addressRepository->add($sicAddress);
            $persistenceManager->persistAll();
        }
        while ($address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses));

        // Migrate images to FAL
        $wizard = new FALImageWizard();
        $wizard->migrateOBGImages();

        $this->redirect("list", "Module");
    }

    /**
     * Migrate from sc_bezugsquelle
     *
     * @return void
     */
    public function migrateBezugsquelleAction()
    {
        // Persistance manager
        $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");

        // Clear database
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_domainproperty");
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_address");
        $GLOBALS['TYPO3_DB']->exec_DELETEquery("sys_category_record_mm", "tablenames = 'tx_sicaddress_domain_model_address'");

        $GLOBALS['TYPO3_DB']->exec_DELETEquery("sys_category", "pid = 445");
        $maxuid = $GLOBALS['TYPO3_DB']->exec_SELECTquery('max(uid)', 'sys_category', '');
        $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE sys_category AUTO_INCREMENT = '.($maxuid+1));

        $GLOBALS['TYPO3_DB']->exec_DELETEquery("sys_file_reference", "tablenames = 'tx_sicaddress_domain_model_address'");
        $maxuid = $GLOBALS['TYPO3_DB']->exec_SELECTquery('max(uid)', 'sys_file_reference', '');
        $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE sys_file_reference AUTO_INCREMENT = '.($maxuid+1));

        // Rename category mm tables
        $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE tx_scbezugsquelle_kategorie CHANGE COLUMN `bezeichnung` `title` VARCHAR(255) NOT NULL DEFAULT ``');
        $GLOBALS['TYPO3_DB']->sql_query('RENAME TABLE tx_scbezugsquelle_kategorie TO tx_sicaddress_domain_model_produkt');
        $GLOBALS['TYPO3_DB']->sql_query('RENAME TABLE tx_scbezugsquelle_bezugsquelle_kategorie_mm TO tx_sicaddress_domain_model_address_produkt_mm');

        // Create Parent category
        $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', array("title" => "Objekte", "pid" => 445));
        $persistenceManager->persistAll();
        $parentID=$GLOBALS['TYPO3_DB']->sql_insert_id();

        // Move legacy category data to sys_category
        $categories = $GLOBALS['TYPO3_DB']->exec_SELECTquery('bezeichnung as title, pid, '.$parentID.' as parent',
            'tx_scbezugsquelle_objekt', 'deleted = 0 AND hidden = 0 AND pid = 445');
        while ($category = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($categories))
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category', $category);

        // Retrieve legacy address data
        $adresses = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'uid, pid, firmenname as company, firmenzusatz, strasse, plz, ort, postfach, postfach_plz, postfach_ort, telefon, fax, email_url as email, link_text, link_url, CAST(logo AS CHAR(10000)) as image, 0 as produkt',
            'tx_scbezugsquelle_bezugsquelle', 'deleted = 0 AND hidden = 0 AND pid = 445', '', 'uid ASC');

        // Add required fields to DomainProperty table
        $address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses);
        foreach($address as $key => $value) {
            if($key == "uid" || $key == "pid")
                continue;

            $domainProperty = new DomainProperty();
            $domainProperty->setProperties($key, "string", $key, "", "", false);
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
            $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE tx_sicaddress_domain_model_address AUTO_INCREMENT = '.$address['uid']);
            $this->addressRepository->add($sicAddress);
            $persistenceManager->persistAll();

            // Insert entry in sys_category_mm
            $uids = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign', 'tx_scbezugsquelle_bezugsquelle_objekt_mm', 'uid_local = '.$address['uid'], '');
            while ($uid = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($uids))
            {
                $title = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('bezeichnung', 'tx_scbezugsquelle_objekt', 'uid = '.$uid['uid_foreign'], ''))["bezeichnung"];
                $localId = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'sys_category', 'title = \''.$title.'\'', '', '', 1))["uid"];

                $mapping = array("uid_local" => $localId,
                    "uid_foreign" => $sicAddress->getUid(),
                    "tablenames" => "tx_sicaddress_domain_model_address",
                    "fieldname" => "categories");
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category_record_mm', $mapping);
            }
        }
        while ($address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses));

        // Migrate images to FAL
        $wizard = new FALImageWizard();
        $wizard->migrateBezugsquelleImages();

        $this->redirect("list", "Module");
    }

    /**
     * action importTTAddress
     *
     * @return void
     */
    public function importTTAddressAction()
    {
        // Clear database
        $GLOBALS['TYPO3_DB']->exec_TRUNCATEquery("tx_sicaddress_domain_model_domainproperty");

        if($this->request->hasArgument("schema") && $this->extensionConfiguration["ttAddressMapping"]) {
            $categories = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COLUMN_NAME', '`INFORMATION_SCHEMA`.`COLUMNS`', 'TABLE_SCHEMA="' . $this->request->getArgument("schema") . '" and TABLE_NAME="tt_address"');
            while ($category = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($categories)) {
                if(preg_match("/^(uid|pid|t3.*|tstamp|hidden|deleted|categories|sorting)$/", $category["COLUMN_NAME"]) === 0) {
                    $domainProperty = new DomainProperty();
                    $domainProperty->setTitle($category["COLUMN_NAME"]);
                    $domainProperty->setTcaLabel($category["COLUMN_NAME"]);
                    $domainProperty->setType("string");
                    $domainProperty->setExternal(!Service::startsWith($category["COLUMN_NAME"], 'tx_'));

                    $this->domainPropertyRepository->add($domainProperty);
                }
            }
        }

        $this->redirect("list", "Module");
    }

    /**
     * Transform key/value array into sic_address entries
     *
     * @param  array $address
     * @return Address
     */
    public function getSicAddrfromLegacyAddr($address)
    {
        $sicaddress = new Address();
        foreach($address as $key => $value) {
            ObjectAccess::setProperty($sicaddress, GeneralUtility::underscoredToLowerCamelCase($key), $value);
        }

        return $sicaddress;
    }
}
