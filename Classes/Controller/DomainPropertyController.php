<?php

namespace SICOR\SicAddress\Controller;


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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use SICOR\SicAddress\Domain\Repository\DomainPropertyRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DomainPropertyController
 */
class DomainPropertyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    protected $objectManager = null;

    /**
     * domainPropertyRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\DomainPropertyRepository
     * @inject
     */
    protected $domainPropertyRepository = NULL;

    /**
     * Persistence Manager
     *
     *@var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     *@inject
     */
    protected $persistenceManager;

    /**
     * action create
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $newDomainProperty
     * @return void
     */
    public function createAction(\SICOR\SicAddress\Domain\Model\DomainProperty $newDomainProperty)
    {
        if (TYPO3_MODE == 'BE') {
            $this->response->setHeader('Content-Type','application/json');

            $errorMessages = [];
            if(!empty($newDomainProperty->getTitle())) {
                $existingObjectWithSameName = $this->domainPropertyRepository->findByTitle(strtolower($newDomainProperty->getTitle()));
                if ($existingObjectWithSameName->count() < 1) {
                    $this->domainPropertyRepository->add($newDomainProperty);
                } else {
                    $errorMessages['title'] = "A field named '" . $newDomainProperty->getTitle() . "' already exists. Please choose a unique name.";
                    return json_encode($errorMessages);
                }
                $this->persistenceManager->persistAll();
            }

            return json_encode(array());
            #$this->redirect('list', 'Module', null, ['errorMessages' => $errorMessages]);
        }
    }

    /**
     * action update
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function updateAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty = null)
    {
        if (TYPO3_MODE == 'BE') {
            $arguments = $this->request->getArgument("domainProperty");
            if(empty($domainProperty)) {
                $domainProperty = $this->domainPropertyRepository->findOneByUid(abs($arguments['__identity']));
                if(empty($arguments['hidden'])) {
                    $domainProperty->setHidden(false);
                }
            }
            if (!array_key_exists("isListLabel", $arguments)) {
                $domainProperty->setIsListLabel(false);
            }
            $this->domainPropertyRepository->update($domainProperty);
            $this->persistenceManager->persistAll();
            $this->response->setHeader('Content-Type','application/json');
            return json_encode(array());
            #$this->redirect('list', 'Module');
        }
    }

    /**
     * action delete
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function deleteAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty = null)
    {
        if (TYPO3_MODE == 'BE') {
            $arguments = $this->request->getArgument("domainProperty");
            if(empty($domainProperty)) {
                $domainProperty = $this->domainPropertyRepository->findOneByUid(abs($arguments));
            }
            if($domainProperty->getType() === 'mmtable') {
                $title = $domainProperty->getTitle();
                $extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sic_address");

                // Delete extra files
                $delFile = $extPath . 'Classes/Domain/Model/' . $title . '.php';
                if (is_file($delFile)) unlink($delFile);
                $delFile = $extPath . 'Configuration/TCA/tx_sicaddress_domain_model_' . strtolower($title) . '.php';
                if (is_file($delFile)) unlink($delFile);
            }
            $this->domainPropertyRepository->remove($domainProperty);
            $this->redirect('list', 'Module');
        }
    }

    public function sortAction() {
        $args = $this->request->getArguments();

        if(!empty($args['ordered'])) {
            $uids = explode(',', $args['ordered']);

            foreach($uids as $sorting=>$uid) {
                if($uid>0) {

                    $property = $this->domainPropertyRepository->findByUid($uid);
                    if($property) {
                        $property->setSorting($sorting);
                        $this->domainPropertyRepository->update($property);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Sortable Wrapper
     * @return config
     */
    public function getFlexSortableFields($config)
    {
        return $this->getFlexFields($config, "integer,float,string,mmtable");
    }

    /**
     * Filter Wrapper
     * @return config
     */
    public function getFlexFilterFields($config)
    {
        return $this->getFlexFields($config, "mmtable");
    }

    /**
     * String Wrapper
     * @return config
     */
    public function getFlexStringFields($config)
    {
        return $this->getFlexFields($config, "string");
    }

    /**
     * Integer Wrapper
     * @return config
     */
    public function getFlexDistanceFields($config)
    {
        return $this->getFlexFields($config, "integer,float");
    }

    /**
     * @return config
     */
    public function getFlexFields($config, $types)
    {
        # auto inject doesnt work here because of direct call as userFunc in flex form
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->domainPropertyRepository = $this->objectManager->get(\SICOR\SicAddress\Domain\Repository\DomainPropertyRepository::class);

        $fields = $this->domainPropertyRepository->findByTypes( explode(',',$types) );

        $optionList = array();
        $optionList[0] = array(0 => 'Ausblenden', 1 => 'none');
        foreach ($fields as $field) {
            $value = ($field["type"] == "mmtable") ? $field["title"] . ".title" : $field["title"];
            $optionList[] = array(0 => $field["tca_label"], 1 => $value);
        }

        $config['items'] = array_merge($config['items'], $optionList);
        return $config;
    }
}
