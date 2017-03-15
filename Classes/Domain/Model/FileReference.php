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


class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder {
    /**
     * Uid of the referenced sys_file. Needed for extbase to serialize the
     * reference correctly.
     *
     * @var int
     */
    protected $uidLocal;

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
    public function getTablenames() {
        return $this->tablenames;
    }

    /**
     * @param string $tablenames
     */
    public function setTablenames($tablenames) {
        $this->tablenames = $tablenames;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceInterface $originalResource
     */
    public function setOriginalResource(\TYPO3\CMS\Core\Resource\ResourceInterface $originalResource) {
        $this->originalResource = $originalResource;
        $this->uidLocal = (int)$originalResource->getUid();
    }

    /**
     * @return \TYPO3\CMS\Core\Resource\FileReference
     */
    public function getOriginalResource() {
        if ($this->originalResource === null) {
            $this->originalResource = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileReferenceObject($this->getUid());
        }

        return $this->originalResource;
    }
}