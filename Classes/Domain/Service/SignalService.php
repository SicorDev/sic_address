<?php
namespace SICOR\SicAddress\Domain\Service;

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
class SignalService implements \TYPO3\CMS\Core\SingletonInterface{

    /**
     * Called after installation
     * Create default generated files
     *
     * @param array $extname
     */
   public function afterExtensionInstall($extname = null) {
      if($extname !== 'sic_address') {
         return;
      }

      // Model
      if (!$this->saveTemplate('Classes/Domain/Model/Address.php', array()))
         $errorMessages[] = "Unable to save Model: Address.php";
      // SQL
      if (!$this->saveTemplate('ext_tables.sql', array()))
         $errorMessages[] = "Unable to save SQL: ext_tables.sql";
      // TCA
      if (!$this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', array()))
         $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_address.php";
      // Language
      if (!$this->saveTemplate('Resources/Private/Language/locallang_db.xlf', array()))
         $errorMessages[] = "Unable to save Locallang: locallang_db.xlf";
      // Table Mapping
      if(!$this->saveTemplate('ext_typoscript_setup.txt', array()))
         $errorMessages[] = "Unable to save Table Mapping: ext_typoscript_setup.txt";
      // Table Mapping Override
      if(!$this->saveTemplate('Configuration/TCA/Overrides/tt_address.php', array()))
         $errorMessages[] = "Unable to save Table Mapping Overrides: tt_address.php";
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
      $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');
      $customView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

      $templatePathAndFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address")) . "Resources/Private/CodeTemplates/" . $filename;
      $customView->setTemplatePathAndFilename($templatePathAndFilename);
      $customView->setPartialRootPath($templatePathAndFilename);
      $customView->assign("settings", array());
      $customView->assign("properties", $properties);
      $content = $customView->render();

      $filename = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address") . $filename;
      return (file_put_contents($filename, $content) !== false);
   }
}
