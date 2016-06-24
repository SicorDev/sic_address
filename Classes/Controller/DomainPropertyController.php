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
 * DomainPropertyController
 */
class DomainPropertyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * domainPropertyRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\DomainPropertyRepository
     * @inject
     */
    protected $domainPropertyRepository = NULL;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $domainProperties = $this->domainPropertyRepository->findAll();
        $this->view->assign('domainProperties', $domainProperties);
    }

    /**
     * action show
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function showAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty)
    {
        $this->view->assign('domainProperty', $domainProperty);
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
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $newDomainProperty
     * @return void
     */
    public function createAction(\SICOR\SicAddress\Domain\Model\DomainProperty $newDomainProperty)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->domainPropertyRepository->add($newDomainProperty);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @ignorevalidation $domainProperty
     * @return void
     */
    public function editAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty)
    {
        $this->view->assign('domainProperty', $domainProperty);
    }

    /**
     * action update
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function updateAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty = null)
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->request->getArguments());
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->domainPropertyRepository->update($domainProperty);
        $this->redirect('list', 'Module');
    }

    /**
     * action delete
     *
     * @param \SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty
     * @return void
     */
    public function deleteAction(\SICOR\SicAddress\Domain\Model\DomainProperty $domainProperty)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->domainPropertyRepository->remove($domainProperty);
        $this->redirect('list');
    }

}