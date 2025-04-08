<?php

namespace SICOR\SicAddress\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 SICOR Devteam <dev@sicor-kdl.net>, Sicor KDL
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

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{
    /**
     * @var string
     */
    protected $tablenames;

    /**
     * @var string
     */
    protected $tableLocal = "sys_file";

    /**
     * @return string
     */
    public function getTablenames()
    {
        return $this->tablenames;
    }

    /**
     * @param string $tablenames
     */
    public function setTablenames($tablenames)
    {
        $this->tablenames = $tablenames;
    }

    public function setOriginalResource(\TYPO3\CMS\Core\Resource\FileReference $originalResource): void
    {
        $this->originalResource = $originalResource;
        $this->uidLocal = (int)$originalResource->getUid();
    }

    public function getOriginalResource(): \TYPO3\CMS\Core\Resource\FileReference
    {
        if ($this->originalResource === null) {
            $this->originalResource = GeneralUtility::makeInstance(ResourceFactory::class)->getFileReferenceObject($this->getUid());
        }

        return $this->originalResource;
    }
}
