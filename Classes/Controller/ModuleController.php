<?php
namespace SICOR\SicAddress\Controller;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . 'Classes/Utility/YamlParser.php');

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
     * Called bevore any action
     */
    public function initializeAction() {
        $this->configuration = spyc_load_file(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . 'Resources/Private/CodeTemplates/DomainProperties.yaml');
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction() {
        $this->view->assign("properties", $this->configuration['DomainProperties']);
    }

    /**
     * action save
     *
     * @return void
     */
    public function saveAction() {
        // Model
        $this->saveTemplate('Classes/Domain/Model/Model.tmpl', $this->configuration['DomainProperties']);
        // SQL
        $this->saveTemplate('ext_tables.sql', $this->getSQLConfiguration());
        // TCA
        $this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php',$this->getTCAConfiguration());
        // Language
        $this->saveTemplate('Resources/Private/Language/locallang_db.xlf', $this->configuration['DomainProperties']);

        $this->redirect('list');
    }

    /**
     * Get SQL Configuration from Model
     *
     * @return array
     */
    private function getSQLConfiguration() {
        $sql = array();
        foreach($this->configuration['DomainProperties'] as $key => $value) {
            switch($value["type"]) {
                case "string":
                    $sql[] = $value["title"] . " " . "varchar(255) DEFAULT '' NOT NULL";
                    break;
                case "integer":
                    $sql[] = $value["title"] . " " . "int(11) unsigned DEFAULT '0' NOT NULL";
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
        foreach($this->configuration['DomainProperties'] as $key => $value) {
            $tca[] = array("title" => $value["title"], "config" => "
                '" . $value["title"] . "' => array(
                'exclude' => 1,
                'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address." . $value["title"] . "',
                'config' => array(
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim'
                ),
            ),        
            ");
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

        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);
        $templatePathAndFilename = $templateRootPath . $path;

        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->assign("properties", $properties);

        $content = $customView->render();

        $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . $path;
        file_put_contents($path, $content) ;
    }

}