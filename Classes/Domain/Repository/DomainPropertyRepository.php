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
        'hidden' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    public function initializeObject()
    {
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setIgnoreEnableFields(TRUE);
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
}
