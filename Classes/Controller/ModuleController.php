<?php
namespace SICOR\SicAddress\Controller;
use SICOR\SicAddress\Domain\Model\DomainProperty;
use SICOR\SicAddress\Utility;

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
     * categoryRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository = NULL;

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
    protected $fieldTypes = array("string", "integer", "select", "image");

    /**
     * Holds the Typoscript configuration
     *
     * @var \TYPO3\CMS\Extbase\Configuration
     */
    protected $extbaseFrameworkConfiguration = NULL;

    /**
     * Holds the Extension configuration
     *
     * @var array
     */
    protected $extensionConfiguration = NULL;

    /**
     * Template Root Path
     *
     * @var string
     */
    protected $templateRootPath = "";

    /**
     * Called before any action
     */
    public function initializeAction() {
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);
        $this->templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);

        $this->configuration = $this->domainPropertyRepository->findAll();
        foreach($this->configuration as $key => $value) {
            //Convert fieldTitles to lowerCamelCase
            $this->configuration[$key]->setTitle(lcfirst($this->configuration[$key]->getTitle()));

            // Initialize Type Objects
            $type = $this->configuration[$key]->getType();
            $class = "SICOR\\SicAddress\\Domain\\Model\\DomainObject\\" . ucfirst($type) . "Type";

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
        $this->view->assign("fieldTypes", $this->getFieldTypeList());
    }

    /**
     * action save
     *
     * @return void
     */
    public function createAction() {
        $errorMessages = array();

        // Model
        if (!$this->saveTemplate('Classes/Domain/Model/Address.php', $this->configuration))
            $errorMessages[] = "Unable to save Model: Address.php";
        // SQL
        if (!$this->saveTemplate('ext_tables.sql', $this->getSQLConfiguration()))
            $errorMessages[] = "Unable to save SQL: ext_tables.sql";
        // TCA
        if (!$this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', $this->getTCAConfiguration()))
            $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_address.php";
        // Language
        if (!$this->saveTemplate('Resources/Private/Language/locallang_db.xlf', $this->configuration))
            $errorMessages[] = "Unable to save Locallang: locallang_db.xlf";
        // Table Mapping
        if(!$this->saveTemplate('ext_typoscript_setup.txt', $this->extensionConfiguration))
            $errorMessages[] = "Unable to save Table Mapping: ext_typoscript_setup.txt";
        // Table Mapping Override
        if(!$this->saveTemplate('Configuration/TCA/Overrides/tt_address.php', $this->getTCAConfiguration()))
            $errorMessages[] = "Unable to save Table Mapping Overrides: tt_address.php";

        $this->updateExtension();
        $this->view->assign("errorMessages", $errorMessages);
    }

    /**
     * @return void
     */
    public function helpAction() {

    }

    /**
     * Clear DomainProperties
     */
    public function removeAllDomainPropertiesAction() {
        $this->domainPropertyRepository->removeAll();

        $this->redirect("list");
    }

    /**
     * Get SQL Configuration from Model
     *
     * @return array
     */
    private function getSQLConfiguration() {
        $sql = array();
        foreach($this->configuration as $key => $value) {
            if(!$value->isExternal()) {
                $sql[] = $value->getType()->getSQLDefinition($value->getTitle());
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
            //If not a default column
            $templatePathAndFilename = $this->templateRootPath . "Resources/Private/Partials/" . ucfirst($value->getType()->getTitle()) . "Type.tca";

            $config = file_get_contents($templatePathAndFilename);

            // TCA Override
            if ($value->getTcaOverride()) {
                $config = $value->getTcaOverride();
            }

            // TCA Settings
            // @TODO -> Allgemeines Konfigurationsformat festlegen
            if ($value->getSettings()) {
                $customView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
                $customView->setTemplatePathAndFilename($templatePathAndFilename);

                $settings = array();
                parse_str($value->getSettings(), $settings);
                $customView->assign("properties", $settings);

                $config = $customView->render();
            }

            $tca[] = array("title" => $value->getTitle(), "config" => $config, "ttAddressMapping" => $this->extensionConfiguration["ttAddressMapping"]);
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

        $templatePathAndFilename = $this->templateRootPath . $filename;
        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->setPartialRootPath($templatePathAndFilename);
        $customView->assign("settings", $this->extensionConfiguration);
        $customView->assign("properties", $properties);
        $content = $customView->render();

        $filename = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . $filename;
        return is_writable($filename) && (file_put_contents($filename, $content) !== false);
    }

    /**
     * Generate FieldTypeList for Select ViewHelper
     *
     * @return array
     */
    private function getFieldTypeList() {
        $result = array();
        foreach($this->fieldTypes as $fieldType) {
            $field = new \StdClass();
            $field->key = $fieldType;
            $field->value = ucfirst($fieldType);

            $result[] = $field;
        }
        return $result;
    }

    /**
     * Clear Cache
     */
    private function updateExtension() {
        $service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
        $service->install($this->request->getControllerExtensionKey());
    }
}