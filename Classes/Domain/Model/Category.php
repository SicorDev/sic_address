<?php
namespace SICOR\SicAddress\Domain\Model;

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
 * Category
 */
class Category extends \TYPO3\CMS\Extbase\Domain\Model\Category
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $sicAddressMarker = NULL;

    /**
     * Initialize images
     *
     * @return \GeorgRinger\News\Domain\Model\Category
     */
    public function __construct()
    {
        $this->sicAddressMarker = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return FileReference[]|\TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getSicAddressMarker() {
        return $this->sicAddressMarker;
    }

    /**
     * @param FileReference $sicAddressMarker
     */
    public function setSicAddressMarker(FileReference $sicAddressMarker) {
        $this->sicAddressMarker = $sicAddressMarker;
    }

    /**
     * @param FileReference $sicAddressMarker
     */
    public function addSicAddressMarker(FileReference $sicAddressMarker) {
        $this->sicAddressMarker->attach($sicAddressMarker);
    }

    /**
     * @param FileReference $sicAddressMarker
     */
    public function removeSicAddressMarker(FileReference $sicAddressMarker) {
        $this->sicAddressMarker->detach($sicAddressMarker);
    }
}
