<?php
namespace SICOR\SicAddress\Controller;
use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Model\DomainProperty;
use SICOR\SicAddress\Utility\FALImageWizard;
use SICOR\SicAddress\Utility\Service;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
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
     * addressRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\AddressRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $addressRepository = NULL;

    /**
     * domainPropertyRepository
     *
     * @var SICOR\SicAddress\Domain\Repository\DomainPropertyRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $domainPropertyRepository = NULL;

    /**
     * action importTTAddress
     *
     * @return void
     */
    public function importTTAddressAction()
    {
        if ($this->extensionConfiguration["ttAddressMapping"]) {
            // Clear database
            $this->domainPropertyRepository->removeAll();
            
            $fields = $this->addressRepository->getFields();
            foreach ($fields as $field) {
                if (preg_match("/^(uid|pid|t3.*|tstamp|hidden|deleted|categories|sorting)$/", $field) === 0) {
                    $domainProperty = new DomainProperty();
                    $domainProperty->setTitle($field);
                    $domainProperty->setTcaLabel($field);
                    $domainProperty->setType(($field === 'image') ? 'image' : 'string');
                    $domainProperty->setExternal(!Service::startsWith($field, 'tx_'));

                    $this->domainPropertyRepository->add($domainProperty);
                }
            }
        }

        $this->redirect("list", "Module");
    }
}
