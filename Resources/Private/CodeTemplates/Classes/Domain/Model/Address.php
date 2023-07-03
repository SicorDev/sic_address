<?php // Auto generated on '{now}'! Do NOT edit !!!
namespace SICOR\SicAddress\Domain\Model;
{namespace sic=SICOR\SicAddress\ViewHelpers}
/***************************************************************
*
*  Copyright notice
*
*  (c) 2016 SICOR DEVTEAM dev@sicor-kdl.net, Sicor KDL GmbH
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
* Address
*/
class Address extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
    * categories
    * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\Category>
    */
    protected $categories = null;

    /**
     * Sortfield
     * @var String
     */
    protected $sortField;

    /**
     * uid
     * @var int
     */
    protected $uid;

    /**
     * hidden
     * @var boolean
     */
    protected $hidden;

    <f:for each="{properties}" as="property">
    <f:format.htmlentitiesDecode>
    /**
     * {property.title}
     * @var {property.type.modelType}
     */
    protected ${property.title};
    </f:format.htmlentitiesDecode>
    </f:for>

    /**
    * __construct
    */
    public function __construct() {
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        <f:for each="{properties}" as="property">
            <f:if condition="{property.type.title} == 'image'">
            $this->{property.title} = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
            </f:if>
        </f:for>
    }

    /**
    * Adds a Category
    *
    * @param \SICOR\SicAddress\Domain\Model\Category $category
    * @return void
    */
    public function addCategory(\SICOR\SicAddress\Domain\Model\Category $category) {
        $this->categories->attach($category);
    }

    /**
    * Removes a Category
    *
    * @param \SICOR\SicAddress\Domain\Model\Category $categoryToRemove The Category to be removed
    * @return void
    */
    public function removeCategory(\SICOR\SicAddress\Domain\Model\Category $categoryToRemove) {
        $this->categories->detach($categoryToRemove);
    }

    /**
    * Returns the categories
    *
    * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\Category> $categories
    */
    public function getCategories() {
        return $this->categories;
    }

    /**
    * Sets the categories
    *
    * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\Category> $categories
    * @return void
    */
    public function setCategories(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories) {
        $this->categories = $categories;
    }

    <f:for each="{properties}" as="property">
    /**
     * @return <f:format.htmlentitiesDecode>{property.type.modelType}</f:format.htmlentitiesDecode>
     */
    public function get<sic:format.case case="capital">{property.title}</sic:format.case>() {
        return $this->{property.title};
    }

    /**
     * @param <f:format.htmlentitiesDecode>{property.type.modelType}</f:format.htmlentitiesDecode> ${property.title}
     * @return void
     */
    public function set<sic:format.case case="capital">{property.title}</sic:format.case>(${property.title}) {
        $this->{property.title} = ${property.title};
    }

    <f:if condition="{property.type.title} == 'image'">
    /**
     * @param \SICOR\SicAddress\Domain\Model\FileReference ${property.title}
     * @return void
     */
    public function add<sic:format.case case="capital">{property.title}</sic:format.case>(\SICOR\SicAddress\Domain\Model\FileReference ${property.title}) {
        $this->{property.title}->attach(${property.title});
    }

    /**
     * @param \SICOR\SicAddress\Domain\Model\FileReference ${property.title}
     * @return void
     */
    public function remove<sic:format.case case="capital">{property.title}</sic:format.case>(\SICOR\SicAddress\Domain\Model\FileReference ${property.title}) {
        $this->{property.title}->detach(${property.title});
    }
    </f:if>
    </f:for>

    /**
     * @return string
     */
    public function getSortField() {
        return $this->sortField;
    }

    /**
     * @param string $sortField
     * @return void
     */
    public function setSortField($sortField) {
        $this->sortField = $sortField;
    }

    /**
     * @return int
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * @param int $uid
     * @return void
     */
    public function setUid($uid) {
        $this->uid = $uid;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden) {
        $this->hidden = $hidden;
    }
}
