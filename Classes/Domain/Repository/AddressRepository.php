<?php
namespace SICOR\SicAddress\Domain\Repository;

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

/**
 * The repository for Addresses
 */
class AddressRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * categoryRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\CategoryRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $categoryRepository = NULL;

    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    /**
     * @return array
     */
    public function getDefaultOrderings()
    {
        return $this->defaultOrderings;
    }

    /**
     * @param array $defaultOrderings
     */
    public function setDefaultOrderings(array $defaultOrderings)
    {
        $this->defaultOrderings = $defaultOrderings;
    }

    public function initializeObject() {
    }

    public function findByPid($pid) {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds(array($pid));
        return $query->execute();
    }

    public function findForVianovis() {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        return $query->execute();
    }

    /**
     * @param $categories
     * @return mixed
     */
    public function findByCategories($categories) {
        //No Categories given -> return all
        if(!$categories) {
            return $this->findAll();
        }

        $query = $this->createQuery();
        $constraints = array();
        foreach($categories as $item) {
            $constraints[] = $query->contains("categories", $item);
        }
        $con = $query->logicalOr($constraints);
        $query->matching($query->logicalAnd($con));

        return $query->execute();
    }

    /**
     * @param $category
     * @param $categoryList
     *
     * @return array
     */
    private function getParents($category, &$categoryList = array()) {
        $category = $this->categoryRepository->findByUid($category);
        $categoryList[] = $category;

        if(!$category->getParent() || ($category->getParent() && $this->categoryRepository->findByParent($category)->count()) > 0) {
            $children = $this->categoryRepository->findByParent($category);

            foreach($children as $child) {
                $this->getParents($child->getUid(), $categoryList);
            }
        }

        return $categoryList;
    }

    /**
     * @param $sql
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function runUpdate($sql) {
        $query = $this->createQuery();
        $query->statement($sql);
        return $query->execute();
    }

    /**
     * @return array
     */
    public function findAtoz($field, $addresstable, $categories, $pages)
    {
        $query = $this->createQuery();

        // Make A-Z respect configured pages if there are some
        $where = "pid<>-1 ";
        if(strlen($pages) > 0)
            $where = "pid IN (".$pages.") ";

        // Standard constraints
        $where .= "AND deleted=0 AND hidden=0 ";

        // Respect categories
        if ($categories && count($categories) > 0) {
            $where .= "AND (";
            foreach ($categories as $category) {
                $where .= "uid IN (SELECT uid_foreign FROM sys_category_record_mm ".
                                                     "WHERE uid_local='".$category->getUid()."' AND sys_category_record_mm.tablenames = '".$addresstable."' ".
                                                     "AND sys_category_record_mm.fieldname = 'categories') OR ";
            }
            $where .= "1=0 )";
        }
        
        $sql = 'select DISTINCT UPPER(LEFT('.$field.', 1)) as letter from ' . $addresstable . ' where ' . $where;

        $res = array();
        foreach($query->statement($sql)->execute(true) as $result) {
            $res[] = $result['letter'];
        }
        
        return $res;
    }

    /**
     * @return array
     */
    public function search($atozvalue, $atozField, $categories, $queryvalue, $queryFields, $distanceValue, $distanceField, $filterValue, $filterField)
    {
        $query = $this->createQuery();

        // Build A to Z constraint
        $constraints = array();
        if ($atozField && !($atozField === "none") && $atozvalue && strlen(trim($atozvalue)) === 1)
            $constraints[] = $query->like($atozField, $atozvalue.'%');

        // Build distance constraint
        if ($distanceField && !($distanceField === "none") && strlen($distanceValue) > 0) {
            $constraints[] = $query->logicalAnd($query->lessThanOrEqual($distanceField, (int)$distanceValue));
        }

        // Build query constraints
        if ($queryFields && count($queryFields) > 0 && $queryvalue && !($queryvalue === ""))
        {
            $queryconstraints = array();
            foreach ($queryFields as $field) {
                $queryconstraints[] = $query->like($field, '%'.$queryvalue.'%');
            }
            $constraints[] = $query->logicalOr($queryconstraints);
        }

        // Build category constraints
        if ($categories && count($categories) > 0)
        {
            $catconstraints = array();
            foreach ($categories as $category) {
                $catconstraints[] = $query->contains("categories", $category->getUid());
            }
            $constraints[] = $query->logicalOr($catconstraints);
        }

        // Build filter constraint
        if ($filterField && !($filterField === "none") && $filterValue > 0)
        {
            // Correction for mmtable
            $filterField = substr($filterField, 0, strpos($filterField, '.'));

            $constraints[] = $query->contains($filterField, $filterValue);
        }

        if(count($constraints) < 1) {
            return $this->findAll();
        }

        $query->matching($query->logicalAnd($constraints));
        return $query->execute();
    }

    /**
     * Find all records with missing coordinates.
     * This method is used in the task.
     */
    public function findGeoLessEntries()
    {
        $query = $this->createQuery();
        $query->setLimit(10);

        $constraints = [
            $query->equals('latitude', null),
            $query->equals('longitude', null)
        ];

        return $query->matching($query->logicalOr($constraints))->execute();
    }

    /**
     * Allow third parties like sic_calender to submit their own queries
     */
    public function findByConstraints($constraints)
    {
        $query = $this->createQuery();
        return $query->matching($constraints)->execute();
    }
}
