<?php
namespace SICOR\SicAddress\Controller;

use In2code\Powermail\ViewHelpers\String\UnderscoredToLowerCamelCaseViewHelper;
use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Model\DomainObject\ImageType;
use SICOR\SicAddress\Domain\Model\DomainObject\MmtableType;
use SICOR\SicAddress\Domain\Model\DomainProperty;
use SICOR\SicAddress\Utility\Service;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * Persistence Manager
     *
     *@var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     *@TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $persistenceManager;


    /**
     * action importTTAddress
     *
     * @return void
     */
    public function importTTAddressAction()
    {
        if ($this->extensionConfiguration["ttAddressMapping"]) {
            // Clear database
            $this->domainPropertyRepository->deleteFieldDefinitions(1);

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

    public function importAction() {
        $this->domainPropertyRepository->initializeRegularObject();
        $this->view->assign('properties', $this->domainPropertyRepository->findAllImportable());
        $this->view->assign('pids', $this->addressRepository->findPids());
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function importFromFileAction() {
        $args = $this->request->getArguments();

        if($args['file']['error']) {
            $this->addFlashMessage(
                $args['file']['name'],
                $this->translate('label_import_error_upload'),
                AbstractMessage::ERROR
            );
            $this->redirect('import');
        }
        if($args['file']['type'] !== 'text/csv') {
            $this->addFlashMessage(
                $args['file']['name'],
                $this->translate('label_import_error_mime'),
                AbstractMessage::ERROR
            );
            $this->redirect('import');
        }

        if (($handle = fopen($args['file']['tmp_name'], "rb")) !== FALSE) {
            $header = array();
            $properties = $this->domainPropertyRepository->findAll();
            $total = $line = 0;
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                if($line === 0) {
                    $this->fillCSVheader($header, $data, $properties);
                    if(empty($header)) {
                        $this->addFlashMessage(
                            $args['file']['name'],
                            $this->translate('label_import_error_csv'),
                            AbstractMessage::ERROR
                        );
                        $this->redirect('import');
                    }
                } else {
                    $address = new Address();
                    $address->setPid($args['pid']);
                    $this->fillAddressWithCSVdata($address, $data, $header, $properties);
                    $this->addressRepository->add($address);
                    $total++;
                }
                $line++;
            }
            $this->persistenceManager->persistAll();
            $this->addFlashMessage(
                $total . ' ' .$this->translate('label_import_total') . ' (' . $args['pids'][ $args['pid'] ] . ')',
                $args['file']['name'],
                AbstractMessage::OK
            );
            $this->redirect('import');
        }
    }

    public function fillCSVheader(&$header, &$data, &$properties) {
        foreach($data as $index=>$val) {
            if(empty($properties[$val])) continue;
            $header[$index]['property'] = GeneralUtility::underscoredToLowerCamelCase($val);
            $header[$index]['field'] = $val;
        }
    }

    public function fillAddressWithCSVdata($address, &$data, &$header, &$properties) {
        foreach($data as $index=>$val) {
            if(empty($properties[  $header[$index]['field']  ])) continue;
            $propertyType = $properties[  $header[$index]['field']  ][0]->getType();
            if($propertyType instanceof ImageType || $propertyType instanceof MmtableType) continue;
            $address->_setProperty($header[$index]['property'], $val);
        }
    }
}
