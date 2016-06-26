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
 * ModuleController
 */
class ModuleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * addressRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\AddressRepository
     * @inject
     */
    protected $addressRepository = NULL;

    /**
     * domainPropertyRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\DomainPropertyRepository
     * @inject
     */
    protected $domainPropertyRepository = NULL;

    /**
     * Holds all domainProperties
     *
     * @var array
     */
    protected $configuration;

    /**
     * Fixed set of fieldTypes
     *
     * @var array
     */
    protected $fieldTypes = array("String, Integer");

    /**
     * Holds the Typoscript configuration
     * 
     * @var \TYPO3\CMS\Extbase\Configuration
     */
    protected $extbaseFrameworkConfiguration = NULL;

    /**
     * Called before any action
     */
    public function initializeAction() {
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        $this->configuration = $this->domainPropertyRepository->findAll();
        foreach($this->configuration as $key => $value) {
            //Convert fieldTitles to lowerCamelCase
            $this->configuration[$key]->setTitle(lcfirst($this->configuration[$key]->getTitle()));

            // Initialize Type Objects
            $type = $this->configuration[$key]->getType();
            $class = "SICOR\\SicAddress\\Domain\\Model\\DomainObject\\".ucfirst($type)."Type";

            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $this->configuration[$key]->setType($objectManager->get($class));
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction() {
        $this->view->assign("properties", $this->configuration);
    }

    /**
     * action save
     *
     * @return void
     */
    public function createAction() {
        $errorMessages = array();

        // Model
        if(!$this->saveTemplate('Classes/Domain/Model/Address.php', $this->configuration))
            $errorMessages[] = "Unable to save Model: Address.php";
        // SQL
        if(!$this->saveTemplate('ext_tables.sql', $this->getSQLConfiguration()))
            $errorMessages[] = "Unable to save SQL: ext_tables.sql";
        // TCA
        if(!$this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', $this->getTCAConfiguration()))
            $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_address.php";
        // Language
        if(!$this->saveTemplate('Resources/Private/Language/locallang_db.xlf', $this->configuration))
            $errorMessages[] = "Unable to save Locallang: locallang_db.xlf";
        
        $this->updateExtension();
        $this->view->assign("errorMessages", $errorMessages);
    }

    /**
     * Get SQL Configuration from Model
     *
     * @return array
     */
    private function getSQLConfiguration() {
        $sql = array();
        foreach($this->configuration as $key => $value) {
            $sql[] = $value->getType()->getSQLDefinition($value->getTitle());
        }
        return $sql;
    }

    /**
     * Get TCA Configuration from Model
     *
     * @return array
     */
    private function getTCAConfiguration() {
        $tca = array();
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);
        foreach($this->configuration as $key => $value) {
            $config = file_get_contents($templateRootPath . "Resources/Private/Partials/" . ucfirst($value->getType()->getTitle()) . "Type.tca");
            $tca[] = array("title" => $value->getTitle(), "config" => $config);
        }
        return $tca;
    }

    /**
     * Save Templates
     *
     * @param $filename
     * @param $properties
     *
     * @return bool
     */
    private function saveTemplate($filename, $properties) {
        $customView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);
        $templatePathAndFilename = $templateRootPath . $filename;

        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->assign("properties", $properties);

        $content = $customView->render();

        $filename = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . $filename;
        if(is_writable($filename) && file_put_contents($filename, $content))
            return true;
        else
            return false;
    }

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
        //@FIXME
        /*$type = $this->fieldTypeRepository->findOneByTitle("string");
        foreach($address as $key => $value) {
           if($key == "pid" || $key == "tstamp" || $key == "crdate" || $key == "cruser_id" || $key == "category")
               continue;

            $domainProperty = new \SICOR\SicAddress\Domain\Model\DomainProperty();
            $domainProperty->setProperties($key, $type, $key, "", "", "", false);
            $this->domainPropertyRepository->add($domainProperty);
        }

        // Enhance everything for new fields (sql, model, tca, ...)
        $persistenceManager->persistAll();
        $this->initializeAction();
        $this->saveAction(false);
        */

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
                $mapping = array("uid_local" => $localId, 'uid_foreign' => $foreignId);
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category_record_mm', $mapping);
            }
        }
        while ($address = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($adresses));
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

    /**
     * Clear Cache
     */
    private function updateExtension() {
        $service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
        $service->install($this->request->getControllerExtensionKey());
    }
}