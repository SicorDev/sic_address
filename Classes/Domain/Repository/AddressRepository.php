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

/**
 * The repository for Addresses
 */
class AddressRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * categoryRepository
     *
     * @var \SICOR\SicAddress\Domain\Repository\CategoryRepository
     * @inject
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

    /**
     * @param $categories
     * @return mixed
     */
    public function findByCategories($categories) {
        //No Categories given -> return all
        if(!$categories) {
            return $this->findAll();
        }

        $result = array();
        foreach ($categories as $category) {
            $parents = $this->getParents($category);
            foreach($parents as $parent) {
                if(!in_array($parent, $result))
                    $result[] = $parent;
            }
        }

        $query = $this->createQuery();
        $constraints = array();
        foreach($result as $item) {
            $constraints[] = $query->contains("categories", $item->getUid());
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
     * @param string $sql
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
        // Make A-Z respect configured pages if there are some
        $where = "pid<>-1 ";
        if(strlen($pages) > 0)
            $where = "pid IN (".$pages.") ";

        // Standard constraints
        $where .= "AND deleted=0 AND hidden=0 ";

        // Respect categories
        if ($categories && count($categories > 0)) {
            $where .= "AND (";
            foreach ($categories as $category) {
                $where .= "uid IN (SELECT uid_foreign FROM sys_category_record_mm WHERE uid_local='".$category->getUid()."' AND sys_category_record_mm.tablenames = 'tt_address' AND sys_category_record_mm.fieldname = 'categories') OR ";
            }
            $where .= "1=0 )";
        }

        $res = array();
        $results = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT UPPER(LEFT('.$field.', 1)) as letter', $addresstable, $where);
        while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($results))	{
            $res[] = $result['letter'];
        }
        return $res;
    }

    /**
     * @return array
     */
    public function search($atozvalue, $atozField, $categories, $queryvalue, $queryFields) {

        $query = $this->createQuery();

        // Build A to Z constraint
        $constraints = array();
        if ($atozField && !($atozField === "none") && $atozvalue && strlen(trim($atozvalue)) === 1)
            $constraints[] = $query->like($atozField, $atozvalue.'%');

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

        if(count($constraints) < 1) {
            return $this->findAll();
        }

        $query->matching($query->logicalAnd($constraints));
        return $query->execute();
    }
}