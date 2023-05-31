<?php

namespace SICOR\SicAddress\EventListener;

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

use TYPO3\CMS\Core\Package\Event\AfterPackageActivationEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;


/**
 * Extension Manager Listener
 */
class ExtensionManagerListener
{
    public function __invoke(AfterPackageActivationEvent $event): void
    {
        if ($event->getPackageKey() == 'sic_address') {
            $this->afterExtensionInstall();
        }
    }

    /**
     * Called after installation
     * Create default generated files
     *
     * @param array $extname
     */
    public function afterExtensionInstall()
    {
        // Create default files
        $this->saveTemplate('Classes/Domain/Model/Address.php', []);
        $this->saveTemplate('Resources/Private/Language/locallang_db.xlf', []);
        $this->saveTemplate('ext_typoscript_setup.typoscript', []);
        $this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', []);

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
        $path = ExtensionManagementUtility::extPath('sic_address');
        $templatePathAndFilename = GeneralUtility::getFileAbsFileName($path) . 'Resources/Private/CodeTemplates/' . $filename;
        $pathAndFilename = $path . $filename;
        if (file_exists($pathAndFilename))
            return true;

        $customView = GeneralUtility::makeInstance(StandaloneView::class);
        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->setPartialRootPaths([$templatePathAndFilename]);
        $customView->assign('settings', []);
        $customView->assign('properties', $properties);
        $content = $customView->render();

        return (file_put_contents($pathAndFilename, $content) !== false);
    }
}
