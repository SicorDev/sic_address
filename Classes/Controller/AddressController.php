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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
        if ($field && !($field === "none")) {
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
        $emptyList = $this->settings['noListStartup'];

        $this->fillAddressList('Alle', $defcat ? $defcat->getUid() : '', '', '', '', '', $emptyList);
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
        $filtervalue = $this->request->hasArgument('filter') ? $this->request->getArgument('filter') : '';
        $this->maincategoryvalue = $this->request->hasArgument('maincategory') ? $this->request->getArgument('maincategory') : '';
        $distanceValue = $this->request->hasArgument('distance') ? $this->request->getArgument('distance') : '';
        $queryvalue = $this->request->hasArgument('query') ? $this->request->getArgument('query') : '';
        $checkall = $this->request->hasArgument('checkall') ? $this->request->getArgument('checkall') : '';
        $this->fillAddressList($atozvalue, $categoryvalue, $filtervalue, $queryvalue, $distanceValue, $checkall);
    }

    /**
     * @param $atozValue
     * @param $categoryValue
     * @param $filterValue
     * @param $queryValue
     * @param $distanceValue
     * @param $checkall
     * @param bool $emptyList
     * @throws \TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException
     */
    public function fillAddressList($atozValue, $categoryValue, $filterValue, $queryValue, $distanceValue, $checkall, $emptyList = false)
    {
        // Categories
        $this->fillCategoryLists($this->configurationManager->getContentObject()->data['uid']);
        $this->view->assign('categories', $this->displayCategoryList);
        $this->view->assign('categoryvalue', $categoryValue);
        $this->view->assign('maincategories', $this->mainCategoryList);
        $this->view->assign('maincategoryvalue', $this->maincategoryvalue);
        $this->view->assign('checkall', $checkall);

        // Filter
        $filterField = $this->settings['filterField'];
        $this->view->assign('filtervalue', $filterValue);
        $this->view->assign('filter', $this->getFilterList($this->searchCategoryList));

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

        if ($emptyList) {
            // No search on startup
            $addresses = null;
        } else {
            // Search addresses
            $searchFields = explode(",", str_replace(' ', '', $this->extensionConfiguration["searchFields"]));
            $addresses = $this->addressRepository->search($atozValue, $atozField, $currentSearchCategories, $queryValue, $searchFields, $distanceValue, $distanceField, $filterValue, $filterField);

            // mmtable resolver
            $field = $this->settings['filterField'];
            if ($field && !is_bool(strpos($field, ".title")))
            {
                $addressPlus = null;
                $field = GeneralUtility::underscoredToLowerCamelCase(substr($field, 0, strpos($field, '.')));

                foreach ($addresses as $address)
                {
                    // Get object representing our mm target
                    $mmObject = ObjectAccess::getProperty($address, $field)->toArray();
                    $iCount = count($mmObject);

                    // Instead of one entry with three filters we create three entries with one filter...
                    for ($i=0; $i<$iCount;  $i++) {
                        // Clone is required, else original is destroyed
                        $addressClone = clone $address;

                        // Same here
                        $storageClone = clone ObjectAccess::getProperty($address, $field);

                        // Silly workaround, as remove all is buggy...
                        while($storageClone->count() > 0)
                            $storageClone->removeAll($storageClone);

                        // Replace mm target with single entry
                        $storageClone->attach($mmObject[$i]);
                        ObjectAccess::setProperty($addressClone, $field, $storageClone);

                        // Set sort string for upcoming usort
                        $addressClone->setSortField($this->normalize($mmObject[$i]->getTitle()));
                        $addressPlus [] = $addressClone;
                    }
                }
                $addresses = $addressPlus;

                // Sort everything once again
                usort($addresses, function ($adr1, $adr2) {
                    return strcmp($adr1->getSortField(), $adr2->getSortField());
                });
            }
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

                // If there's no children just use the category defined by uid
                if(count($this->displayCategoryList) == 0)
                    $this->displayCategoryList[] = $this->categoryRepository->findByUid($uid);

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
        $this->sortCategories($this->displayCategoryList);
        $this->sortCategories($this->mainCategoryList);
    }

    /**
     * Sort Helper
     *
     * @return sorted list of categories
     */
    public function sortCategories(&$categoryList)
    {
        if ($categoryList) {
            usort($categoryList, function ($cat1, $cat2) {
                $str1 = $this->normalize($cat1->getTitle());
                $str2 = $this->normalize($cat2->getTitle());
                return strcmp($str1, $str2);
            });
        }
    }

    /**
     * Sort Helper
     *
     * @return string
     */
    public static function normalize($str)
    {
        $str = str_replace( 'ß', "ss", $str);
        $str = str_replace( 'ä', "ae", $str);
        $str = str_replace( 'ö', "oe", $str);
        $str = str_replace( 'ü', "ue", $str);
        $str = str_replace( 'Ä', "Ae", $str);
        $str = str_replace( 'Ö', "Oe", $str);
        $str = str_replace( 'Ü', "Ue", $str);
        return $str;
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
        $arguments = $this->request->getArguments();
        if($arguments['image']) {
            $fileReference = FALService::uploadFalFile($arguments['image'], 'avatars/test/test/', "tx_sicasyl_domain_model_personaldata", "image");
            $newAddress->getPersonalData()->setImage($fileReference);
        }
        $this->addFlashMessage('Adresse wurde erstellt', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->addressRepository->add($newAddress);
        $this->redirect('new');
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
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRoot = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPaths'][0]);

        switch ($this->extensionConfiguration["templateSet"])
        {
            case 'nicosdir': $this->view->setTemplatePathAndFilename($templateRoot.'Address/NicosList.html'); break;
            case 'spdir': $this->view->setTemplatePathAndFilename($templateRoot.'Address/SPDirList.html'); break;
            case 'wtdir': $this->view->setTemplatePathAndFilename('Not Implemented'); break;
            case 'irsee': $this->view->setTemplatePathAndFilename($templateRoot.'Address/IrseeList.html'); break;
            case 'mmdir': $this->view->setTemplatePathAndFilename($templateRoot.'Address/MMList.html'); break;
            case 'obgdir': $this->view->setTemplatePathAndFilename($templateRoot.'Address/OBGList.html'); break;
            case 'sachon': $this->view->setTemplatePathAndFilename($templateRoot.'Address/SachonList.html'); break;
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

    /**
     *  Create filter Info for Fluid Template
     */
    private function getFilterList($categories)
    {
        // Get config
        $field = $this->settings['filterField'];
        if ($field && $field === "none") return null;

        // Correction for mmtable
       $field = substr($field, 0, strpos($field, '.'));

        // Query Database
        $filterList = array();
        $res = $this->addressRepository->search('', '', $categories, '', '', '', '', '', '');

        // Build filter list
        foreach ($res as $address) {
            $filters = ObjectAccess::getProperty($address, GeneralUtility::underscoredToLowerCamelCase($field));
            foreach ($filters as $filter) {
                $filterList[] = $filter;
            }
        }

        $this->sortCategories($filterList);
        return $filterList;
    }
}
