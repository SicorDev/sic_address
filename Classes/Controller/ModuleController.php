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
     * @var array
     */
    protected $configuration;

    /**
     * addressRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\AddressRepository
     * @inject
     */
    protected $addressRepository = NULL;

    /**
     * fieldTypeRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\FieldTypeRepository
     * @inject
     */
    protected $fieldTypeRepository = NULL;

    /**
     * domainPropertyRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\DomainPropertyRepository
     * @inject
     */
    protected $domainPropertyRepository = NULL;

    /**
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
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction() {
        $this->view->assign("properties", $this->configuration);
        $this->view->assign("fieldTypes", $this->fieldTypeRepository->findAll());
    }

    /**
     * action save
     *
     * @return void
     */
    public function createAction() {
        // Model
        $this->saveTemplate('Classes/Domain/Model/Address.php', $this->configuration);
        // SQL
        $this->saveTemplate('ext_tables.sql', $this->getSQLConfiguration());
        // TCA
        $this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', $this->getTCAConfiguration());
        // Language
        $this->saveTemplate('Resources/Private/Language/locallang_db.xlf', $this->configuration);

        $this->updateExtension();

        $this->view->assign("alert", "Successfully updated Extension");
    }

    /**
     * Get SQL Configuration from Model
     *
     * @return array
     */
    private function getSQLConfiguration() {
        $sql = array();
        foreach($this->configuration as $key => $value) {
            switch($value->getType()) {
                case "string" || "map" || "image":
                    $sql[] = $value->getTitle() . " " . "varchar(255) DEFAULT '' NOT NULL";
                    break;
                case "integer":
                    $sql[] = $value->getTitle() . " " . "int(11) unsigned DEFAULT '0' NOT NULL";
                    break;
                default:
                    break;
            }
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
        foreach($this->configuration as $key => $value) {
            if($value->getType()->getTitle() == "string" || $value->getType()->getTitle() == "map" || $value->getType()->getTitle() == "integer") {
                $tca[] = array("title" => $value->getTitle(), "config" => "
                    '" . $value->getTitle() . "' => array(
                    'exclude' => 1,
                    'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address." . $value->getTitle() . "',
                    'config' => array(
                        'type' => 'input',
                        'size' => 30,
                        'eval' => 'trim'
                    ),
                ),
                ");
            }else if($value->getType()->getTitle() == "image") {
                $tca[] = array("title" => $value->getTitle(), "config" => "
                    '" . $value->getTitle() . "' => array(
                    'exclude' => 1,
                    'label' => 'Image',
                    'config' => \\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility::getFileFieldTCAConfig('image', array(
                            'appearance' => array(
                                    'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                            ),
                            'minitems' => 0,
                            'maxitems' => 1,
                    )," . $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] ." ),
                ),
                ");
            }
        }
        return $tca;
    }

    /**
     * Save templates
     *
     * @param $path
     * @param $properties
     */
    private function saveTemplate($path, $properties) {
        $customView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);
        $templatePathAndFilename = $templateRootPath . $path;

        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->assign("properties", $properties);

        $content = $customView->render();

        $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . $path;
        file_put_contents($path, $content) ;
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
        $type = $this->fieldTypeRepository->findOneByTitle("string");
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