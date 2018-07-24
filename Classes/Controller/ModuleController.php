<?php

namespace SICOR\SicAddress\Controller;

use SICOR\SicAddress\Domain\Model\DomainProperty;
use SICOR\SicAddress\Utility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Mvc\View\NotFoundView;

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
class ModuleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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
    protected $fieldTypes = array("string", "integer", "select", "image", "rich", "boolean", "float", "checklist", "mmtable");

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
    public function initializeAction()
    {
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);
        $this->templateRootPath = GeneralUtility::getFileAbsFileName($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);

        $this->configuration = $this->domainPropertyRepository->findAll();
        foreach ($this->configuration as $key => $value) {
            // Initialize Type Objects
            $type = $this->configuration[$key]->getType();
            $class = "SICOR\\SicAddress\\Domain\\Model\\DomainObject\\" . ucfirst($type) . "Type";

            $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $this->configuration[$key]->setType($objectManager->get($class));
        }

        $this->setBackendModuleTemplates();
    }

    /**
     * Set Backend Module Templates
     * @return void
     */
    protected function setBackendModuleTemplates()
    {
        $viewConfiguration = array(
            'view' => array(
                'templateRootPath' => 'EXT:sic_address/Resources/Private/Backend/Templates/',
                'partialRootPath' => 'EXT:sic_address/Resources/Private/Backend/Partials/',
                'layoutRootPath' => 'EXT:sic_address/Resources/Private/Backend/Layouts/',
            )
        );
        $this->configurationManager->setConfiguration(array_merge($this->extbaseFrameworkConfiguration, $viewConfiguration));
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        // Bailout if static template is missing
        if ($this->view instanceof NotFoundView) {
            if (!$this->extbaseFrameworkConfiguration['view'] || !array_key_exists('templateRootPaths', $this->extbaseFrameworkConfiguration['view'])) {
                echo "Static typoscript template hasn't been included in this branch.";
                exit;
            }
        }
        if ($this->request->hasArgument('errorMessages')) {
            $this->view->assign("errorMessages", $this->request->getArgument('errorMessages'));
        }
        $this->view->assign("properties", $this->configuration);
        $this->view->assign("fieldTypes", $this->getFieldTypeList());
        $this->view->assign("address", $this->addressRepository->findAll());
    }

    /**
     * action save
     *
     * @return void
     */
    public function createAction()
    {
        $errorMessages = array();

        // Model
        $domainProperties = array();
        foreach ($this->configuration as $key => $value) {
            $title = GeneralUtility::underscoredToLowerCamelCase($value->getTitle());
            $value->getType()->setClassName($title);

            if ($value->getType()->getTitle() === "mmtable") {
                if (!$this->saveTemplate("Classes/Domain/Model/" . $title . ".php", $value, "Classes/Domain/Model/Table.php"))
                    $errorMessages[] = "Unable to save Model: " . $title . ".php";
                if (!$this->saveTemplate("Configuration/TCA/tx_sicaddress_domain_model_" . strtolower($title) . ".php", $this->getSingleTCAConfiguration($value), "Configuration/TCA/tx_sicaddress_domain_model_table.php"))
                    $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_" . strtolower($title) . ".php";
            }

            $domainProperties[$key] = clone $value;
            $domainProperties[$key]->setTitle($title);
        }

        if (!$this->saveTemplate('Classes/Domain/Model/Address.php', $domainProperties))
            $errorMessages[] = "Unable to save Model: Address.php";

        // SQL
        if (!$this->saveTemplate('ext_tables.sql', $this->getSQLConfiguration()))
            $errorMessages[] = "Unable to save SQL: ext_tables.sql";

        // TCA
        $extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address");
        if ($this->extensionConfiguration["ttAddressMapping"]) {
            // tt_address override
            if (!$this->saveTemplate('Configuration/TCA/Overrides/tt_address.php', $this->getTCAConfiguration()))
                $errorMessages[] = "Unable to save Table Mapping Overrides: tt_address.php";
            $delFile = $extPath . 'Configuration/TCA/tx_sicaddress_domain_model_address.php';
            if (is_file($delFile)) unlink($delFile);
        } else {
            // tx_sicaddress_domain_model_address
            $tca = $this->getTCAConfiguration();

            // Create orderby and headline according to config
            $headline = "";
            $orderby = [];
            foreach ($tca as $value) {
                if ($value['isListLabel']) {
                    $orderby[] = $value['title'];
                    if (empty($headline))
                        $headline = $value['title'];
                }
            }
            $orderbyquery = empty($orderby) ? '' : "ORDER BY " . implode(',', $orderby);

            if (!$this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', $this->getTCAConfiguration(), "", $headline, $orderbyquery))
                $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_address.php";
            $delFile = $extPath . 'Configuration/TCA/Overrides/tt_address.php';
            if (is_file($delFile)) unlink($delFile);
        }

        // Language
        if (!$this->saveTemplate('Resources/Private/Language/locallang_db.xlf', $this->configuration))
            $errorMessages[] = "Unable to save Locallang: locallang_db.xlf";

        // Table Mapping
        if (!$this->saveTemplate('ext_typoscript_setup.txt', $this->extensionConfiguration))
            $errorMessages[] = "Unable to save Table Mapping: ext_typoscript_setup.txt";

        $this->updateExtension();
        $this->view->assign("errorMessages", $errorMessages);
    }

    /**
     * Delete DomainProperties
     * @param string $external
     */
    public function deleteFieldDefinitionsAction($external)
    {
        $this->domainPropertyRepository->deleteFieldDefinitions($external);
        $this->redirect("list");
    }

    /**
     * Get SQL Configuration from Model
     *
     * @return array
     */
    private function getSQLConfiguration()
    {
        $sql = array();
        foreach ($this->configuration as $key => $value) {
            if (!$value->isExternal()) {
                $sql[$key]["definition"] = $value->getType()->getSQLDefinition('`'.$value->getTitle().'`');
                $sql[$key]["title"] = $value->getTitle();
                $sql[$key]["type"] = $value->getType($value->getTitle());
            }
        }
        return $sql;
    }

    /**
     * Get TCA Configuration from Model
     *
     * @return array
     */
    private function getTCAConfiguration()
    {
        $tca = array();
        foreach ($this->configuration as $key => $value) {
            $tca[] = $this->getSingleTCAConfiguration($value);
        }
        return $tca;
    }

    /**
     * Get single TCA Configuration from Model
     *
     * @param $value
     * @return array
     */
    private function getSingleTCAConfiguration($value)
    {
        // Build options for dropdowns or checklists
        $options = "";
        $settings = array();
        if ($value->getType()->getTitle() === "select" || $value->getType()->getTitle() === "checklist") {
            // Dropdown options
            if (!empty($value->getSettings())) {
                $dropdownOptions = explode("\n", $value->getSettings());
                for ($i = 0; $i < count($dropdownOptions); $i++) {
                    $options .= 'array("' . trim($dropdownOptions[$i]) . '", ' . ($i + 1) . '),';
                }
            }
        } else {
            // Settings
            parse_str($value->getSettings(), $settings);
        }

        // Render the TCA snippets
        $templatePathAndFilename = $this->templateRootPath . "Resources/Private/Partials/" . ucfirst($value->getType()->getTitle()) . "Type.tca";
        $customView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $customView->setTemplatePathAndFilename($templatePathAndFilename);

        $customView->assign("properties", $settings);
        $customView->assign("options", $options);
        $customView->assign("title", $value->getTitle());
        $config = $customView->render();

        // Eventually override TCA
        if ($value->getTcaOverride()) {
            $config = $value->getTcaOverride();
        }

        $tca = array("external" => $value->isExternal(), "title" => $value->getTitle(), "tcaLabel" => $value->getTcaLabel(), "type" => $value->getType(), "isListLabel" => $value->getIsListLabel(), "config" => $config);
        return $tca;
    }


    /**
     * Save Templates
     *
     * @param $filename
     * @param $properties
     * @param $templatePath
     *
     * @return bool
     */
    private function saveTemplate($filename, $properties, $templatePath = "", $headline = "", $orderbyquery = "")
    {
        $customView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $templatePathAndFilename = !$templatePath ? $this->templateRootPath . $filename : $this->templateRootPath . $templatePath;
        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->setPartialRootPaths([$templatePathAndFilename]);
        $customView->assign("settings", $this->extensionConfiguration);
        $customView->assign("properties", $properties);
        $customView->assign("headline", $headline);
        $customView->assign("orderbyquery", $orderbyquery);

        $content = $customView->render();
        $filename = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . $filename;

        return (boolean)file_put_contents($filename, $content);
    }

    /**
     * Generate FieldTypeList for Select ViewHelper
     *
     * @return array
     */
    private function getFieldTypeList()
    {
        $result = array();
        foreach ($this->fieldTypes as $fieldType) {
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
    private function updateExtension()
    {
        $service = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
        $service->install($this->request->getControllerExtensionKey());
    }
}
