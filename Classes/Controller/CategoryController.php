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
 * CategoryController
 */
class CategoryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * categoryRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\CategoryRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $categoryRepository = NULL;
    
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $categories = $this->categoryRepository->findAll();
        $this->view->assign('categories', $categories);
    }
    
    /**
     * action show
     *
     * @param \SICOR\SicAddress\Domain\Model\Category $category
     * @return void
     */
    public function showAction(\SICOR\SicAddress\Domain\Model\Category $category)
    {
        $this->view->assign('category', $category);
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
     * @param \SICOR\SicAddress\Domain\Model\Category $newCategory
     * @return void
     */
    public function createAction(\SICOR\SicAddress\Domain\Model\Category $newCategory)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->categoryRepository->add($newCategory);
        $this->redirect('list');
    }
    
    /**
     * action edit
     *
     * @param \SICOR\SicAddress\Domain\Model\Category $category
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $category
     * @return void
     */
    public function editAction(\SICOR\SicAddress\Domain\Model\Category $category)
    {
        $this->view->assign('category', $category);
    }
    
    /**
     * action update
     *
     * @param \SICOR\SicAddress\Domain\Model\Category $category
     * @return void
     */
    public function updateAction(\SICOR\SicAddress\Domain\Model\Category $category)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->categoryRepository->update($category);
        $this->redirect('list');
    }
    
    /**
     * action delete
     *
     * @param \SICOR\SicAddress\Domain\Model\Category $category
     * @return void
     */
    public function deleteAction(\SICOR\SicAddress\Domain\Model\Category $category)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->categoryRepository->remove($category);
        $this->redirect('list');
    }

}