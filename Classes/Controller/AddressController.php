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

use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Repository\AddressRepository;
use SICOR\SicAddress\Domain\Repository\CategoryRepository;
use SICOR\SicAddress\Domain\Repository\ContentRepository;
use SICOR\SicAddress\Domain\Service\GeocodeService;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use SICOR\SicAddress\Domain\Service\FALService;
use SICOR\SicAddress\Utility\Service;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use function array_filter;
use function class_exists;
use function count;
use function explode;
use function ksort;

/**
 * AddressController
 */
class AddressController extends AbstractController
{
    protected ?AddressRepository $addressRepository;
    protected ?ContentRepository $contentRepository;
    protected ?CategoryRepository $categoryRepository;
    protected ?GeocodeService $geocodeService;
    protected Typo3QuerySettings $querySettings;
    protected Service $service;

    protected array $extensionConfiguration;
    protected $displayCategoryList = null;
    protected $mainCategoryList = null;
    protected $maincategoryvalue = '';
    protected $searchCategoryList = null;
    protected $addresstable = '';

    public function __construct(
        AddressRepository $addressRepository,
        ContentRepository $contentRepository,
        CategoryRepository $categoryRepository,
        GeocodeService $geocodeService,
        Typo3QuerySettings $querySettings,
        Service $service
    )
    {
        $this->addressRepository = $addressRepository;
        $this->contentRepository = $contentRepository;
        $this->categoryRepository = $categoryRepository;
        $this->geocodeService = $geocodeService;
        $this->querySettings = $querySettings;
        $this->service = $service;
    }


    public function initializeAction(): void
    {
        // Init config
        $this->extensionConfiguration = Service::getConfiguration();
        $this->addresstable = $this->extensionConfiguration['ttAddressMapping'] ? 'tt_address' : 'tx_sicaddress_domain_model_address';

        // Init sorting
        $field = $this->settings['sortField'];
        $direction = $this->settings['sortDirection'];
        if ($field && !($field === "none")) {
            $this->addressRepository->setDefaultOrderings(array(
                $field => $direction !== 'desc' ? QueryInterface::ORDER_ASCENDING : QueryInterface::ORDER_DESCENDING
            ));
        }

        // Make search respect configured pages if there are some
        $pages = $this->configurationManager->getContentObject()->data['pages'];

        if (strlen($pages) > 0) {
            $this->querySettings->setRespectStoragePage(TRUE);
            $this->querySettings->setStoragePageIds(explode(',', $pages));
        } else {
            $this->querySettings->setRespectStoragePage(FALSE);
        }

        $this->addressRepository->setDefaultQuerySettings($this->querySettings);

        // Include js
        $GLOBALS['TSFE']->additionalFooterData['tx_sicaddress_sicaddress'] = '<script src="typo3conf/ext/sic_address/Resources/Public/Javascript/sicaddress.js" type="text/javascript"></script>';
    }

    public function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $this->maincategoryvalue = '';
        $defcat = $this->categoryRepository->findByUid($this->settings['categoryDefault']);
        $emptyList = $this->settings['noListStartup'];

        $this->fillAddressList($this->translate('label_all'), $defcat ? $defcat->getUid() : '', '', '', '', '', $emptyList);
    }

    /**
     * @return array|mixed
     */
    protected function getDistances() {
        $distances = array();
        foreach(explode(',', $this->settings['distances']) as $distance) {
            $distances[$distance] = $distance . ' km';
        }

        return $distances;
    }

    public function getCategoriesAndChildren(int $mainCategory, array $currentCategories) : array
    {
        $categories = array();
        $this->categoryRepository->setDefaultOrderings(array(
            'sorting' => QueryInterface::ORDER_ASCENDING
        ));

        foreach($this->categoryRepository->findByParent($mainCategory) as $index=>$category) {
            $categoryTitle = $category->getTitle();

            $children = $this->categoryRepository->findByParent($category->getUid());
            $category = array(
                'uid' => $category->getUid()
            );
            foreach($children as $child) {
                $active = in_array($child->getUid(),$currentCategories);
                $args = array(
                    'currentCategories' => $currentCategories
                );
                $args['currentCategories'][\rawurlencode($categoryTitle)] = $active ? 0 : $child->getUid();
                $args['marker'] = $child->getSicAddressMarker()->current();

                $category['children'][$child->getTitle()] = array(
                    'uid' => $child->getUid(),
                    'active' => $active,
                    'arguments' => $args
                );
            }

            $categories[$categoryTitle] = $category;
        }

        return $categories;
    }

    /**
     * @return Address
     */
    public function getCenterAddressObjectFromFlexConfig(): ?Address
    {
        if(!empty($this->settings['centerAddress'])) {
            $arr = explode('_',$this->settings['centerAddress']);
            $centerAddressUid = array_pop($arr);

            return $this->addressRepository->findByUid($centerAddressUid);
        }

        return new Address();
    }

    public function searchAction()
    {
        if(!empty($this->settings['ignoreDemands'])) {
            return $this->listAction();
        }

        $atozvalue = $this->request->hasArgument('atoz') ? $this->request->getArgument('atoz') : $this->translate('label_all');
        $categoryvalue = $this->request->hasArgument('category') ? $this->request->getArgument('category') : '';
        $filtervalue = $this->request->hasArgument('filter') ? $this->request->getArgument('filter') : '';
        $this->maincategoryvalue = $this->request->hasArgument('maincategory') ? $this->request->getArgument('maincategory') : '';
        $distanceValue = $this->request->hasArgument('distance') ? $this->request->getArgument('distance') : '';
        $queryvalue = $this->request->hasArgument('query') ? trim($this->request->getArgument('query')) : '';
        $checkall = $this->request->hasArgument('checkall') ? $this->request->getArgument('checkall') : '';

        $this->fillAddressList($atozvalue, $categoryvalue, $filtervalue, $queryvalue, $distanceValue, $checkall);
        $this->view->assign('listPageUid', $GLOBALS['TSFE']->id);
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
        if($this->settings['categoryType'] === 'groups') {
            $this->displayCategoryList = $this->getCategoriesAndChildren($this->settings['rootCategory'], explode(',', $categoryValue));
        }
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
            $addresses = [];
        } elseif (!empty($this->settings['selectedAddresses'])) {
            // Display configured addresses
            $selectedAdresses = explode(',', $this->settings['selectedAddresses']);
            foreach ($selectedAdresses as $address) {
                $addresses[] = $this->addressRepository->findByUid($address);
            }
        } else {
            // Search addresses
            $searchFields = explode(",", str_replace(' ', '', $this->extensionConfiguration["searchFields"]));
            $addresses = $this->addressRepository->search($atozValue, $atozField, $currentSearchCategories, $queryValue, $searchFields, $distanceValue, $distanceField, $filterValue, $filterField);

            /* Sachon speciality only... can be removed once it's offline...
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

                // Sort everything once again (actually groups by filter!)
                if(count($addresses) > 1) {
                    usort($addresses, function ($adr1, $adr2) {
                        return strcmp($adr1->getSortField(), $adr2->getSortField());
                    });
                }
            }
            Sachon speciality only... can be removed once it's offline... */

            // Handle pagination
            $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
            $itemsPerPage = (int)(($this->settings['itemsPerPage'] ?? '') ?: 10);
            $paginator = GeneralUtility::makeInstance(QueryResultPaginator::class, $addresses, $currentPage, $itemsPerPage);
            $paginationClass = $paginationConfiguration['class'] ?? SimplePagination::class;
            if (class_exists($paginationClass)) {
                $pagination = GeneralUtility::makeInstance($paginationClass, $paginator);
            } else {
                $pagination = GeneralUtility::makeInstance(SimplePagination::class, $paginator);
            }
            $this->view->assignMultiple([
                'pagination' => [
                    'currentPage' => $currentPage,
                    'paginator' => $paginator,
                    'pagination' => $pagination,
                ]
            ]);
        }

        if($this->settings['categoryType'] !== 'groups') {
            // Count adresses per category phase 1
            $categorycounts = [];
            foreach ($addresses as $adress) {
                foreach ($adress->getCategories() as $category) {
                    if(array_key_exists($category->getTitle(), $categorycounts)) {
                        $categorycounts[$category->getTitle()]++;
                    }
                    else {
                        $categorycounts[$category->getTitle()] = 1;
                    }
                }
            }
            // Count adresses per category phase 2
            foreach ($this->displayCategoryList as $category) {
                if(array_key_exists($category->getTitle(), $categorycounts)) {
                    $category->setCount($categorycounts[$category->getTitle()]);
                }
            }
        }

        $this->view->assign('addresses', $addresses);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('contentUid', $this->configurationManager->getContentObject()->data['uid']);

        $this->addMapProperties($addresses);

        $this->setConfiguredTemplate();
    }

    public function addMapProperties(iterable $addresses): void
    {
        $args = $this->request->getArguments();
        if(!array_key_exists('distance', $args)) {
            $args['distance'] = null;
        }

        $currentCountry = $arg['country'] ?? "Deutschland";

        /** @var Address $centerAddress */
        $centerAddress = $this->service->getCenterAddressObjectFromFlexConfig();
        $center = false;
        if($centerAddress->getLatitude() === '' || $centerAddress->getLongitude() === '') {
            $center = $this->geocodeService->getCoordinatesForPostalCode($args['center'], $currentCountry);
            if($center['longitude'] > 0 && $center['latitude'] > 0) {
                $centerAddress->setLongitude($center['longitude']);
                $centerAddress->setLatitude($center['latitude']);
            }
        }

        if($args['distance']) {
            $lat = $centerAddress->getLatitude();
            $lon = $centerAddress->getLongitude();
            foreach ($addresses as $address) {
                $dist = $this->getDistanceinKM($lat, $lon, $address->getLatitude(), $address->getLongitude());
                $address->setPosition($dist > $args['distance'] ? '1' : '0');
            }
        }

        $countries = array_filter($this->addressRepository->findAllCountries());
        if(empty($args['country']) && count($countries) > 0) {
            $args['country'] = array_key_first($countries);
        }
        ksort($countries);

        $this->view->assignMultiple([
            'addresses' => $addresses,
            'args' => $args,
            'countries' => $countries,
            'centerAddress' => $centerAddress,
            'centerNotFound' => !$center && !empty($args['center']),
            'distances' => $this->getDistances(),
            'radius' => $args['distance'],
        ]);
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

        $ce = $this->contentRepository->findByUid($uid);
        $categories = $ce->getCategoryUids();
        $count = count($categories);

        if ($count == 1) {
            if ($this->settings['mainCategoryType'] !== "none") {
                // Fill main category list
                $uid = array_pop($categories);
                $this->mainCategoryList = $this->categoryRepository->findByParent($uid)->toArray();

                // Display children of main category
                if ($this->maincategoryvalue != '') {
                    $this->displayCategoryList = $this->categoryRepository->findByParent($this->maincategoryvalue)->toArray();
                }

                // Search children of selected category
                $this->searchCategoryList = $this->displayCategoryList;
            } else {
                // Display children of selected category
                $uid = array_pop($categories);
                $this->displayCategoryList = $this->categoryRepository->findByParent($uid)->toArray();

                // If there's no children just use the category defined by uid
                if(count($this->displayCategoryList) == 0)
                    $this->displayCategoryList[] = $this->categoryRepository->findByUid($uid);

                // Search children of selected category
                $this->searchCategoryList = $this->displayCategoryList;
            }
        } elseif ($count > 1) {
            foreach($categories as $uid) {
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
        if ($categoryList && count($categoryList) > 1) {
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
     * @param \SICOR\SicAddress\Domain\Model\Address|null $address
     * @return void
     */
    public function showAction(\SICOR\SicAddress\Domain\Model\Address $address = null)
    {
        if(empty($address)) {
            $uid = $this->settings['singleAddress'];
            if(!empty($this->settings['singleTtAddress'])) {
                $uid = $this->settings['singleTtAddress'];
            }
            $addresses = array();
            foreach(explode(',', $uid) as $id) {
                if(empty($id)) continue;
                $id = abs($id);
                $item = $this->addressRepository->findByUid($id);
                if($item) $addresses[] = $item;
            }
            if(count($addresses) > 1) {
                $this->request->setControllerActionName('showMultiple');
                $this->view->setControllerContext($this->getControllerContext());
                $this->view->assign('addresses', $addresses);
                die($this->view->render());
            }

            $address = $this->addressRepository->findByUid($uid);
        }

        $listPageUid = $this->settings['listPageField'];
        if(empty($listPageUid)) {
            if($this->request->hasArgument('listPageUid')) {
                $listPageUid = $this->request->getArgument('listPageUid');
            } else {
                $listPageUid = 0;
            }
        }

        $this->view->assign('address', $address);
        $this->view->assign('listPageUid', $listPageUid);

        if($this->request->hasArgument('vcard') && $this->request->getArgument('vcard')) {
            $this->view->assign('charset', \strtolower(mb_internal_encoding()));

            $description = $address->getDescription();
            $description = strip_tags($description);
            $description = \html_entity_decode($description);
            $description = \str_replace(
                array(';',"\r","\n"),
                array('\;','','\n'),
                $description
            );
            $address->setDescription($description);
            $this->view->assign('address', $address);

            $this->request->setFormat('vcf');
            $this->view->setControllerContext($this->getControllerContext());
            header('Content-type: text/vcard');

            $filename = $this->request->hasArgument('filename') ? $this->request->getArgument('filename') : '';
            if(empty($filename)) {
                $filename = method_exists($address,'getName') ? $address->getName() : $address->getUid();
            }
            header('Content-Disposition: attachment; filename="' . $filename . '.vcf"');

            die ($this->view->render());
        }
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
        if ($arguments["images"]) {
            foreach ($arguments["images"] as $image) {
                $fileReference = FALService::uploadFalFile($image, 'sic_address', $this->addresstable, "image");
                if ($fileReference)
                    $newAddress->addImage($fileReference);
            }
        }

        $this->addFlashMessage($this->translate('label_address_created'), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->addressRepository->add($newAddress);
        $this->redirect('new');
    }

    /**
     *  Switch templates based on config
     */
    public function setConfiguredTemplate()
    {
        $template = '';
        switch ($this->extensionConfiguration["templateSet"])
        {
            case 'sicor': $template = 'SicorList.html'; break;
            case 'map': $template = 'MapList.html'; break;
            case 'auto': $template = 'AutoList.html'; break;
            case 'nicosdir': $template = 'NicosList.html'; break;
            case 'spdir': $template = 'SPDirList.html'; break;
            case 'diakonie': $template = 'DiakonieList.html'; break;
            case 'duelmen': $template = 'DuelmenList.html'; break;
            case 'irsee': $template = 'IrseeList.html'; break;
            case 'massiv': $template = 'MassivList.html'; break;
            case 'muniges': $template = 'UnigesList.html'; break;
            case 'obgdir': $template = 'OBGList.html'; break;
            case 'sachon': $template = 'SachonList.html'; break;
            case 'ualdir': $template = 'UALList.html'; break;
        }
        if (method_exists($this->view, 'setTemplate')) {
            $this->view->setTemplate($template);
        }
    }

    /**
     *  Create A-Z Info for Fluid Template
     */
    private function getAtoz($categories)
    {
        // Get config
        $field = $this->settings['atozField'];
        if (empty($field) || $field === "none") return null;
        $pages = $this->configurationManager->getContentObject()->data['pages'];

        // Query Database
        $res = $this->addressRepository->findAtoz($field, $this->addresstable, $categories, $pages);

        // Build two dimensional result array
        $atoz = array();
        foreach (range("A", "Z") as $char) {
            $atoz[] = array("character" => $char, "active" => (array_search($char, $res) !== false));
        }

        // Add 'All'
        if (count($res) > 0)
            $atoz[] = array("character" => $this->translate('label_all'), "active" => true);

        return $atoz;
    }

    /**
     *  Create filter Info for Fluid Template
     */
    private function getFilterList($categories)
    {
        // Get config
        $field = $this->settings['filterField'];
        if (empty($field) || $field === "none") return null;

        if (strpos($field, '.') !== false) {
            // Correction for mmtable
            $field = substr($field, 0, strpos($field, '.'));
        }
        $field = GeneralUtility::underscoredToLowerCamelCase($field);

        $fieldType = $this->addressRepository->getFieldType($field);
        if($fieldType === 'string') {
            // Query Database
            $res = $this->addressRepository->getFilterArray($field);

            // Build filter list
            $filterList = [];
            foreach ($res as $filter) {
                $filterList[$filter[$field]] = $filter[$field];
            }
        }
        else {
            // Query Database
            $res = $this->addressRepository->search('', '', $categories, '', '', '', '', '', '');

            // Build filter list
            $filterList = [];
            foreach ($res as $address) {
                $filters = ObjectAccess::getProperty($address, $field);
                if (empty($filters)) continue;

                // Filter field is type mmtable
                foreach ($filters as $filter) {
                    $filterList[$filter->getTitle()] = $filter->getUid();
                }
            }
        }

        // Sort filters alphabetically
        uksort($filterList, function ($filter1, $filter2) {
            $a = preg_replace('#[^\w\s]+#', '', iconv('utf-8', 'ascii//TRANSLIT', $filter1));
            $b = preg_replace('#[^\w\s]+#', '', iconv('utf-8', 'ascii//TRANSLIT', $filter2));
            return strcmp($a, $b);
        });

        return $filterList;
    }

    /**
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     *
     * @return float [km]
     */
    function getDistanceinKM($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad)
            * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad)
            * cos($latitudeTo * $rad) * cos($theta * $rad);

        return acos($dist) / $rad * 60 *  1.853;
    }
}
