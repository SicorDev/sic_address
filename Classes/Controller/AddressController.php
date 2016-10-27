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

    // Other variables
    protected $displayCategoryList = null;
    protected $mainCategoryList = null;
    protected $maincategoryvalue = '';
    protected $searchCategoryList = null;


    /**
     * Called before any action
     */
    public function initializeAction()
    {
        // Init config
        $this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sic_address']);

        // Init sorting
        $field = $this->settings['sortField'];
        if (!($field === "none")) {
            $this->addressRepository->setDefaultOrderings(array(
                $field => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
            ));
        }

        // Make search respect configured pages if there are some
        $pages = $this->configurationManager->getContentObject()->data['pages'];
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');

        if (strlen($pages) > 0) {
            $querySettings->setRespectStoragePage(TRUE);
            $querySettings->setStoragePageIds(explode(',', $pages));
        } else {
            $querySettings->setRespectStoragePage(FALSE);
        }

        $this->addressRepository->setDefaultQuerySettings($querySettings);

        // Include js
        $GLOBALS['TSFE']->additionalFooterData['tx_sicaddress_sicaddress'] = '<script src="typo3conf/ext/sic_address/Resources/Public/Javascript/sicaddress.js" type="text/javascript"></script>';
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $this->maincategoryvalue = '';
        $defcat = $this->addressRepository->findByUid($this->settings['categoryDefault']);
        $this->fillAddressList('Alle', $defcat ? $defcat->getUid() : '', '', '', '');
    }

    /**
     * action search
     *
     * @return void
     */
    public function searchAction()
    {
        $atozvalue = $this->request->hasArgument('atoz') ? $this->request->getArgument('atoz') : 'Alle';
        $categoryvalue = $this->request->hasArgument('category') ? $this->request->getArgument('category') : '';
        $this->maincategoryvalue = $this->request->hasArgument('maincategory') ? $this->request->getArgument('maincategory') : '';
        $distanceValue = $this->request->hasArgument('distance') ? $this->request->getArgument('distance') : '';
        $queryvalue = $this->request->hasArgument('query') ? $this->request->getArgument('query') : '';
        $checkall = $this->request->hasArgument('checkall') ? $this->request->getArgument('checkall') : '';
        $this->fillAddressList($atozvalue, $categoryvalue, $queryvalue, $distanceValue, $checkall);
    }

    public function fillAddressList($atozValue, $categoryValue, $queryValue, $distanceValue, $checkall)
    {
        // Categories
        $this->fillCategoryLists($this->configurationManager->getContentObject()->data['uid']);
        $this->view->assign('categories', $this->displayCategoryList);
        $this->view->assign('categoryvalue', $categoryValue);
        $this->view->assign('maincategories', $this->mainCategoryList);
        $this->view->assign('maincategoryvalue', $this->maincategoryvalue);
        $this->view->assign('checkall', $checkall);

        // Atoz
        $atozField = $this->settings['atozField'];
        $this->view->assign('atozvalue', $atozValue);
        $this->view->assign('atoz', $this->getAtoz($this->searchCategoryList));

        // Distance
        $distanceField = $this->settings['distanceField'];
        $this->view->assign('distancevalue', $distanceValue);

        // Query
        $this->view->assign('queryvalue', $queryValue);

        // Default: Search in configured places
        $currentSearchCategories = $this->searchCategoryList;
        if ($categoryValue > 0) {
            $currentSearchCategories = array();

            if (strpos($categoryValue, ',') !== false) {
                // If checkboxes where clicked, search in selected ones
                $uids = explode(",", $categoryValue);
                foreach ($uids as $uid) {
                    $category = $this->categoryRepository->findByUid($uid);
                    $currentSearchCategories[] = clone $category;
                }
            } else {
                // If a category was selected, search there instead...
                $category = $this->categoryRepository->findByUid($categoryValue);
                $currentSearchCategories[] = clone $category;

                // and its children
                if ($category->getParent() == null) {
                    $currentSearchCategories = array_merge($currentSearchCategories, $this->categoryRepository->findByParent($categoryValue)->toArray());
                }
            }
        }

        if (($this->settings['mainCategoryType'] !== "none") && ($this->maincategoryvalue < 1)) {
            // No search until a main category is chosen
            $addresses = null;
        } else {
            // Search addresses
            $searchFields = explode(",", str_replace(' ', '', $this->extensionConfiguration["searchFields"]));
            $addresses = $this->addressRepository->search($atozValue, $atozField, $currentSearchCategories, $queryValue, $searchFields, $distanceValue, $distanceField);
        }

        $this->view->assign('addresses', $addresses);
        $this->view->assign('settings', $this->settings);

        $this->setConfiguredTemplate();
    }

    /**
     * Return categories as configured by the according tt_content element
     * @param $uid of the content element
     */
    public function fillCategoryLists($uid)
    {
        $this->mainCategoryList = null;
        $this->displayCategoryList = null;
        $this->searchCategoryList = null;

        $results = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'sys_category_record_mm', "tablenames = 'tt_content' AND uid_foreign = " . $uid);
        $count = $GLOBALS['TYPO3_DB']->sql_num_rows($results);

        if ($count == 1) {
            if ($this->settings['mainCategoryType'] !== "none") {
                // Fill main category list
                $uid = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($results)['uid_local'];
                $this->mainCategoryList = $this->categoryRepository->findByParent($uid)->toArray();

                // Display children of main category
                if ($this->maincategoryvalue != '') {
                    $this->displayCategoryList = $this->categoryRepository->findByParent($this->maincategoryvalue)->toArray();
                }

                // Search children of selected category
                $this->searchCategoryList = $this->displayCategoryList;
            } else {
                // Display children of selected category
                $uid = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($results)['uid_local'];
                $this->displayCategoryList = $this->categoryRepository->findByParent($uid)->toArray();

                // Search children of selected category
                $this->searchCategoryList = $this->displayCategoryList;
            }
        } elseif ($count > 1) {
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($results)) {
                $uid = $row['uid_local'];
                $category = $this->categoryRepository->findByUid($uid);

                // Display selected categories
                $this->displayCategoryList[] = clone $category;

                // Search selected categories...
                $this->searchCategoryList[] = clone $category;

                // ...and their children
                if ($category->getParent() == null) {
                    $this->searchCategoryList = array_merge($this->searchCategoryList, $this->categoryRepository->findByParent($uid)->toArray());
                }
            }
        } else {
            // Display all categories
            $this->displayCategoryList = $this->categoryRepository->findAll()->toArray();
        }

        // Alphasort displayed lists
        if ($this->displayCategoryList) {
            usort($this->displayCategoryList, function ($cat1, $cat2) {
                return strcasecmp($cat1->getTitle(), $cat2->getTitle());
            });
        }
        if ($this->mainCategoryList) {
            usort($this->mainCategoryList, function ($cat1, $cat2) {
                return strcasecmp($cat1->getTitle(), $cat2->getTitle());
            });
        }
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction()
    {
        $address = $this->addressRepository->findByUid($this->settings['singleAddress']);
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
     *  Switch templates based on config
     */
    public function setConfiguredTemplate()
    {
        switch ($this->extensionConfiguration["templateSet"]) {
            case 'nicosdir':
                $this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:sic_address/Resources/Private/Templates/Address/NicosList.html'));
                break;
            case 'spdir':
                $this->view->setTemplatePathAndFilename('Not Implemented');
                break;
            case 'wtdir':
                $this->view->setTemplatePathAndFilename('Not Implemented');
                break;
            case 'mmdir':
                $this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:sic_address/Resources/Private/Templates/Address/MMList.html'));
                break;
            case 'company':
                $this->view->setTemplatePathAndFilename('Not Implemented');
                break;
        }
    }

    /**
     *  Create A-Z Info for Fluid Template
     */
    private function getAtoz($categories)
    {
        // Get config
        $field = $this->settings['atozField'];
        if ($field === "none") return null;
        $addresstable = $this->extensionConfiguration['ttAddressMapping'] ? 'tt_address' : 'tx_sicaddress_domain_model_address';
        $pages = $this->configurationManager->getContentObject()->data['pages'];

        // Query Database
        $res = $this->addressRepository->findAtoz($field, $addresstable, $categories, $pages);

        // Build two dimensional result array
        $atoz = array();
        foreach (range("A", "Z") as $char) {
            $atoz[] = array("character" => $char, "active" => (array_search($char, $res) !== false));
        }

        // Add 'Alle'
        if (count($res) > 0)
            $atoz[] = array("character" => "Alle", "active" => true);

        return $atoz;
    }
}
