<?php
namespace SICOR\SicAddress\Domain\Model;



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
class Address extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
    * categories
    *
    * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\Category>
    */
    protected $categories = null;

    
    /**
    * firstName
    *
    * @var string
    */
    protected $firstName;
    
    /**
    * lastName
    *
    * @var string
    */
    protected $lastName;
    
    /**
    * test
    *
    * @var string
    */
    protected $test;
    
    /**
    * test2
    *
    * @var string
    */
    protected $test2;
    

    /**
    * __construct
    */
    public function __construct()
    {
    //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
    * Initializes all ObjectStorage properties
    * Do not modify this method!
    * It will be rewritten on each save in the extension builder
    * You may modify the constructor of this class instead
    *
    * @return void
    */
    protected function initStorageObjects()
    {
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
    * Adds a Category
    *
    * @param \SICOR\SicAddress\Domain\Model\Category $category
    * @return void
    */
    public function addCategory(\SICOR\SicAddress\Domain\Model\Category $category)
    {
        $this->categories->attach($category);
    }

    /**
    * Removes a Category
    *
    * @param \SICOR\SicAddress\Domain\Model\Category $categoryToRemove The Category to be removed
    * @return void
    */
    public function removeCategory(\SICOR\SicAddress\Domain\Model\Category $categoryToRemove)
    {
        $this->categories->detach($categoryToRemove);
    }

    /**
    * Returns the categories
    *
    * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\Category> $categories
    */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
    * Sets the categories
    *
    * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SICOR\SicAddress\Domain\Model\Category> $categories
    * @return void
    */
    public function setCategories(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    
    /**
    * Returns the firstName
    *
    * @return string
    */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
    * Sets the firstName
    *
    * @param string $firstName
    * @return void
    */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    /**
    * Returns the lastName
    *
    * @return string
    */
    public function getLastName() {
        return $this->lastName;
    }

    /**
    * Sets the lastName
    *
    * @param string $lastName
    * @return void
    */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    
    /**
    * Returns the test
    *
    * @return string
    */
    public function getTest() {
        return $this->test;
    }

    /**
    * Sets the test
    *
    * @param string $test
    * @return void
    */
    public function setTest($test) {
        $this->test = $test;
    }
    
    /**
    * Returns the test2
    *
    * @return string
    */
    public function getTest2() {
        return $this->test2;
    }

    /**
    * Sets the test2
    *
    * @param string $test2
    * @return void
    */
    public function setTest2($test2) {
        $this->test2 = $test2;
    }
    

}
