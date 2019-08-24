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
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    public function findAtoz($field, $addresstable, $categories, $pages, $ttAddressMapping)
    {
        $query = $this->createQuery();

        // TODO: Get it from $context with 9 LTS+ version only
        $currentLanguageUid = (int) $GLOBALS['TSFE']->sys_language_uid;

        // Make A-Z respect configured pages if there are some
        $where = "pid<>-1 ";
        if(strlen($pages) > 0)
            $where = "pid IN (".$pages.") ";

        $currentLanguageUid = (int) $GLOBALS['TSFE']->sys_language_uid;

        // Standard constraints
        $where .= "AND deleted=0 AND hidden=0 ";
        if(empty($ttAddressMapping))
            $where .= "AND sys_language_uid IN (-1,".$currentLanguageUid .") ";

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
     * @param $centerAddress
     * @param $distance
     * @param $currentCategories
     * @param $currentCountry
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findGeoEntries($centerAddress, $distance, $currentCategories, $currentCountry) {
        $query = $this->createQuery();

        if(!empty($centerAddress) && !empty($distance)) {
            $lat = $centerAddress->getLatitude() / 180 * M_PI;
            $lon = $centerAddress->getLongitude() / 180 * M_PI;

            $having = $limit = '';
            if(is_numeric($distance)) {
                $having = 'HAVING distance <= ' . $distance;
            } else {
                $having = 'HAVING distance > 0';
                $limit = 'LIMIT 1';
            }

            $sql = <<< SQL
                SELECT *,(6368 * SQRT(2*(1-cos(RADIANS(latitude)) * 
                cos($lat) * (sin(RADIANS(longitude)) *
                sin($lon) + cos(RADIANS(longitude)) * 
                cos($lon)) - sin(RADIANS(latitude)) *
                sin($lat)))) AS distance
                FROM tt_address
                WHERE
                  hidden = 0
                  AND
                  deleted = 0
                $having
                ORDER BY distance
                $limit
SQL;

            return $query->statement($sql)->execute();
        }

        $constraints = array(
            $query->logicalNot(
                $query->logicalOr(
                    $query->equals('latitude', null),
                    $query->equals('longitude', null)
                )
            )
        );

        foreach($currentCategories as $currentCategory) {
            if($currentCategory) {
                $constraints[] = $query->contains('categories', $currentCategory);
            }
        }

        if(!empty($currentCountry)) {
            $constraints[] = $query->equals('country', $currentCountry);
        }

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }

    /**
     * Allow third parties like sic_calender to submit their own queries
     */
    public function findByConstraints($constraints)
    {
        $query = $this->createQuery();
        return $query->matching($constraints)->execute();
    }

    /**
     * Get all fields from tt_address
     */
    public function getFields() {        
        if(!empty($GLOBALS['TYPO3_DB'])) {

            $GLOBALS['TYPO3_DB']
            ->exec_INSERTquery('tt_address', array(
                'company' => 'SICOR'
            ));

            $rows = $GLOBALS['TYPO3_DB']
            ->exec_SELECTgetRows('*', 'tt_address', '', '', '', 1);

            $GLOBALS['TYPO3_DB']
            ->exec_DELETEquery('tt_address', 'company = "SICOR"');

            return array_keys(array_pop($rows));

        } else {

            $connection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getConnectionForTable('tt_address');

            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder
            ->insert('tt_address')
            ->values(array(
                'company' => 'SICOR'
            ))
            ->execute();

            $queryBuilder = $connection->createQueryBuilder();
            $rows = $queryBuilder
            ->select('*')
            ->from('tt_address')
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->execute()
            ->fetchAll();

            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder
            ->delete('tt_address')
            ->where('company = "SICOR"')
            ->execute();

            return array_keys(array_pop($rows));
            
        }        
    }

    /**
     * Find doublets in address entries
     *
     * @param array $fields The db fields to search for doublets.
     * @return void
     */
    public function findDoublets($fields) {
        $properties = [];
        foreach($fields as $field=>$active) {
            if(!empty($active)) {
                $properties[] = $field;
            }
        }
        if(!empty($properties)) {
            $selectProperties = [];
            foreach($properties as $property) {
                $selectProperties[] = 'IF('.$property.' IS NULL,"(NULL)",'.$property.') AS '.$property;
            }
            $selectProperties = implode(',', $selectProperties);
            $properties = implode(',', $properties);

            $query = $this->createQuery();
            $table = $query->getSource()->getSelectorName();
            $where = '1=1';
            if(empty($fields['hidden'])) {
                $where .= ' AND hidden=0';
            }
            if(empty($fields['deleted'])) {
                $where .= ' AND deleted=0';
            }

            $sql = '
            select
                count(uid) as total,'.$selectProperties.'
            from
                '.$table.'
            where
                '.$where.'
            group by
                '.$properties.'
                having total > 1
            order by
                deleted desc,
                hidden desc,
                total desc,' . $properties;

            $doublets = [];
            foreach($query->statement($sql)->execute(true) as $doublet) {
                $pid = empty($fields['pid']) ? 0 : $doublet['pid'];
                if(isset($doublet['pid'])) {
                    unset($doublet['pid']);
                }
                $doublets[$pid]['page'] = $this->getPageNameLabel($pid);
                $doublets[$pid]['datasets'][] = $doublet;
            }
            return $doublets;
        }

        return [];
    }

    /**
     * Get page name from its pid
     *
     * @param int $pid The pid value of the page
     * @return string
     */
    public function getPageNameLabel($pid = 0) {
        if(isset($this->pages[$pid])) {
            return $this->pages[$pid];
        }

        $query = $this->createQuery();
        $sql = 'select uid,hidden,deleted,title from pages where uid = '.abs($pid);

        $page = $query->statement($sql)->execute(true);
        $this->pages[$pid] = empty($page) ? [] : $page[0];

        return $this->pages[$pid];
    }

    /**
     * Find address entries matching the values of the given arguments
     *
     * @param array $args An array of fields with their values included; array('field1' => 'value1', 'field2' => 'value2', ...)
     * @return void
     */
    public function findByArgs($args) {
        $constraints  = array();
        $query = $this->createQuery();

        foreach($args as $field=>$value) {
            if( $value == '(NULL)' ) $value = '';

            if( !empty($value) && !in_array($field, array('action','controller')) ) {
                $property = GeneralUtility::underscoredToLowerCamelCase($field);
                $constraints[] = $query->equals($property, $value);
            }
        }

        $query->matching($query->logicalAnd($constraints));
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }

    /**
     * Return the table of for query object
     *
     * @return void
     */
    public function getTable() {
        return $this->createQuery()->getSource()->getSelectorName();
    }

    /**
     * Check if given property is relevant.
     * - not a system field
     * - has not empty values
     *
     * @param array $property
     * @return boolean
     */
    public function isRelevant($property) {
        if(in_array($property, array('crdate','tstamp','l10n_diffsource','cruser_id'))) return false;

        $query = $this->createQuery();
        $table = $query->getSource()->getSelectorName();

        $sql = 'SELECT COUNT(' . $property . ') AS total FROM ' . $table . ' WHERE LENGTH(TRIM(' . $property . ')) > 0 AND TRIM(' . $property . ') != "0" GROUP BY ' . $property;
        $res = $query->statement($sql)->execute(true);

        return !empty($res[0]);
    }
}
