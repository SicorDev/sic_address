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

/**
 * AddressController
 */
class AddressController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * addressRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\AddressRepository
     * @inject
     */
    protected $addressRepository = NULL;

    /**
     * categoryRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository = NULL;

    /**
     * Holds the Extension configuration
     *
     * @var array
     */
    protected $extensionConfiguration = NULL;

    /**
     * Called before any action
     */
    public function initializeAction() {
        $this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $this->fillAddressList('alle', '', '');
    }

    /**
     * action search
     *
     * @return void
     */
    public function searchAction()
    {
        $atozvalue = $this->request->hasArgument('atoz') ? $this->request->getArgument('atoz') : 'alle';
        $categoryvalue = $this->request->hasArgument('category') ? $this->request->getArgument('category') : '';
        $queryvalue = $this->request->hasArgument('query') ? $this->request->getArgument('query') : '';
        $this->fillAddressList($atozvalue, $categoryvalue, $queryvalue);
    }

    public function fillAddressList($atozvalue, $categoryvalue, $queryvalue)
    {
        $atozField = $this->settings['atozField'];
        $this->view->assign('atoz', $this->getAtoz());
        $this->view->assign('atozvalue', $atozvalue);

        $categories = $this->categoryRepository->findAll();
        $this->view->assign('categories', $categories);
        $this->view->assign('categoryvalue', $categoryvalue);

        $queryField = $this->settings['queryField'];
        $queryactive = !($queryField === "none");
        $this->view->assign('queryactive', $queryactive);
        $this->view->assign('queryvalue', $queryvalue);

        $addresses = $this->addressRepository->search($atozvalue, $atozField, $categoryvalue, $queryvalue, $queryField);

        $this->view->assign('addresses', $addresses);

        $this->setConfiguredTemplate();
    }

    /**
     * action show
     *
     * @param \SICOR\SicAddress\Domain\Model\Address $address
     * @return void
     */
    public function showAction(\SICOR\SicAddress\Domain\Model\Address $address)
    {
        $this->view->assign('address', $address);
    }
    
    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
        
    }
    
    /**
     * action create
     *
     * @param \SICOR\SicAddress\Domain\Model\Address $newAddress
     * @return void
     */
    public function createAction(\SICOR\SicAddress\Domain\Model\Address $newAddress)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->addressRepository->add($newAddress);
        $this->redirect('list');
    }
    
    /**
     * action edit
     *
     * @param \SICOR\SicAddress\Domain\Model\Address $address
     * @ignorevalidation $address
     * @return void
     */
    public function editAction(\SICOR\SicAddress\Domain\Model\Address $address)
    {
        $this->view->assign('address', $address);
    }
    
    /**
     * action update
     *
     * @param \SICOR\SicAddress\Domain\Model\Address $address
     * @return void
     */
    public function updateAction(\SICOR\SicAddress\Domain\Model\Address $address)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->addressRepository->update($address);
        $this->redirect('list');
    }
    
    /**
     * action delete
     *
     * @param \SICOR\SicAddress\Domain\Model\Address $address
     * @return void
     */
    public function deleteAction(\SICOR\SicAddress\Domain\Model\Address $address)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->addressRepository->remove($address);
        $this->redirect('list');
    }


    /**
     *  Switch temaplates based on config
     */
    public function setConfiguredTemplate() {
        switch($this->extensionConfiguration["templateSet"]) {
            case 'nicosdir': $this->view->setTemplatePathAndFilename('typo3conf\ext\sic_address\Resources\Private\Templates\Address\NicosList.html'); break;
            case 'spdir': $this->view->setTemplatePathAndFilename('Not Implemented'); break;
            case 'wtdir': $this->view->setTemplatePathAndFilename('Not Implemented'); break;
            case 'mmdir': $this->view->setTemplatePathAndFilename('Not Implemented'); break;
            case 'company': $this->view->setTemplatePathAndFilename('Not Implemented'); break;
        }
    }

    /**
     *  Create A-Z Info for Fluid Template
     */
    private function getAtoz()
    {
        // Get config
        $field = $this->settings['atozField'];
        if($field === "none") return null;

        // Query Database
        $res = $this->addressRepository->findAtoz($field);

        // Build two dimensional result array
        $atoz = array();
        foreach(range("A","Z") as $char) {
            $atoz[] =  array ("character" => $char, "active" => (array_search($char, $res) !== false));
        }

        // Add 'alle'
        if(count($res) > 0)
            $atoz[] =  array ("character" => "alle", "active" => true);

        return $atoz;
    }
}
