<?php

namespace SICOR\SicAddress\Domain\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\ClassLoadingInformation;
use TYPO3\CMS\Backend\Utility\BackendUtility;

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
 * SignalService
 */
class SignalService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Called after installation
     * Create default generated files
     *
     * @param array $extname
     */
    public function afterExtensionInstall($extname = null)
    {
        if ($extname !== 'sic_address') {
            return;
        }

        // Create default files
        $this->saveTemplate('Classes/Domain/Model/Address.php', array());
        $this->saveTemplate('Resources/Private/Language/locallang_db.xlf', array());
        $this->saveTemplate('ext_typoscript_setup.typoscript', array());
        $this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', array());

        // 'Autoload' above classes...
        ClassLoadingInformation::dumpClassLoadingInformation();
    }

    /**
     * Save Templates
     *
     * @param $filename
     * @param $properties
     *
     * @return bool
     */
    private function saveTemplate($filename, $properties)
    {
        // Build filenames
        $templatePathAndFilename = GeneralUtility::getFileAbsFileName(ExtensionManagementUtility::extPath("sic_address")) . "Resources/Private/CodeTemplates/" . $filename;
        $filename = ExtensionManagementUtility::extPath("sic_address") . $filename;
        if (file_exists($filename))
            return true;

        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $customView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->setPartialRootPaths([$templatePathAndFilename]);
        $customView->assign("settings", array());
        $customView->assign("properties", $properties);
        $content = $customView->render();

        return (file_put_contents($filename, $content) !== false);
    }

    /**
     * Called after saving extension configuration
     * Refresh module list when backend module is activated
     *
     * @param string $extensionKey
     * @param array $newConfiguration
     */
    public function refreshModuleList($extensionKey, $newConfiguration)
    {
        if ($extensionKey === 'sic_address') {
            BackendUtility::setUpdateSignal('updateModuleMenu');
        }
    }
}
