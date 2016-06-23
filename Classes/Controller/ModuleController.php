<?php
namespace SICOR\SicAddress\Controller;

require_once(PATH_site . 'typo3conf/ext/sic_address/Classes/Utility/YamlParser.php');

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


    public function initializeAction() {
        $this->configuration = spyc_load_file(PATH_site . 'typo3conf/ext/sic_address/Resources/Private/Backend/CodeTemplates/DomainProperties.yaml');
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
        $emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
        $templatePathAndFilename = $templateRootPath . '/var/www/virtual/extension.playground.sicor-kdl.local/htdocs/typo3conf/ext/sic_address/Resources/Private/Backend/CodeTemplates/Classes/Domain/Model/Model.tmpl';


        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assign("properties", $this->configuration['DomainProperties']);

        $content = $emailView->render();

        $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . "Classes/Domain/Model/Address2.php";
        file_put_contents($path, $content) ;
    }

}