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
 * The repository for DomainProperties
 */
class DomainPropertyRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'sys_language_uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'hidden' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    public function initializeObject()
    {
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setIgnoreEnableFields(TRUE);
        $querySettings->setRespectSysLanguage(FALSE);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param mixed $types
     */
    public function findByTypes($types) {
        $query = $this->createQuery();

        if( is_array($types) ) {
            $query->matching(
                $query->in('type', $types)
            );
        } else {
            $query->matching(
                $query->equals('type', $types)
            );
        }

        return $query->execute(true);
    }

    /**
     * Delete either all custom or all tt_address definitions.
     * @param string $external
     */
    public function deleteFieldDefinitions($external)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('external', $external)
        );

        foreach($query->execute() as $item) {
            $this->remove($item);
        }
    }

    /**
     * Return sys_language items
     *
     * @return array
     */
    public function getSysLanguages() {
        $query = $this->createQuery();

        $sql = 'select uid,flag,title,language_isocode as iso from sys_language where hidden=0 order by uid';

        $languages = array(0 => '');
        foreach($query->statement($sql)->execute(true) as $row) {
            $languages[ $row['uid'] ] = $row;
        }
        return $languages;
    }

    /**
     *
     * @param int $external
     * @return void
     */
    public function findByExternal($external) {
        $query = $this->createQuery();
        $external = abs($external);

        $query->matching(
            $query->equals('external', $external)
        );

        return $this->groupByLanguages($query);
    }

    public function findAll() {
        $query = $this->createQuery();

        return $this->groupByLanguages($query);
    }

    private function groupByLanguages($query) {
        $properties = array();

        foreach($query->execute() as $property) {
            $title = $property->getTitle();
            if(empty($title)) continue;
            $sysLanguage = $property->_getProperty('_languageUid');            
            $properties[$title][$sysLanguage] = $property;
        }

        return $properties;
    }
}
