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
    * company
    *
    * @var string
    */
    protected $company;
    
    /**
    * street
    *
    * @var string
    */
    protected $street;
    
    /**
    * city
    *
    * @var string
    */
    protected $city;
    
    /**
    * tel
    *
    * @var string
    */
    protected $tel;
    
    /**
    * fax
    *
    * @var string
    */
    protected $fax;
    
    /**
    * email
    *
    * @var string
    */
    protected $email;
    
    /**
    * www
    *
    * @var string
    */
    protected $www;
    
    /**
    * image
    *
    * @var string
    */
    protected $image;
    

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
    * Returns the company
    *
    * @return string
    */
    public function getCompany() {
        return $this->company;
    }

    /**
    * Sets the company
    *
    * @param string $company
    * @return void
    */
    public function setCompany($company) {
        $this->company = $company;
    }
    
    /**
    * Returns the street
    *
    * @return string
    */
    public function getStreet() {
        return $this->street;
    }

    /**
    * Sets the street
    *
    * @param string $street
    * @return void
    */
    public function setStreet($street) {
        $this->street = $street;
    }
    
    /**
    * Returns the city
    *
    * @return string
    */
    public function getCity() {
        return $this->city;
    }

    /**
    * Sets the city
    *
    * @param string $city
    * @return void
    */
    public function setCity($city) {
        $this->city = $city;
    }
    
    /**
    * Returns the tel
    *
    * @return string
    */
    public function getTel() {
        return $this->tel;
    }

    /**
    * Sets the tel
    *
    * @param string $tel
    * @return void
    */
    public function setTel($tel) {
        $this->tel = $tel;
    }
    
    /**
    * Returns the fax
    *
    * @return string
    */
    public function getFax() {
        return $this->fax;
    }

    /**
    * Sets the fax
    *
    * @param string $fax
    * @return void
    */
    public function setFax($fax) {
        $this->fax = $fax;
    }
    
    /**
    * Returns the email
    *
    * @return string
    */
    public function getEmail() {
        return $this->email;
    }

    /**
    * Sets the email
    *
    * @param string $email
    * @return void
    */
    public function setEmail($email) {
        $this->email = $email;
    }
    
    /**
    * Returns the www
    *
    * @return string
    */
    public function getWww() {
        return $this->www;
    }

    /**
    * Sets the www
    *
    * @param string $www
    * @return void
    */
    public function setWww($www) {
        $this->www = $www;
    }
    
    /**
    * Returns the image
    *
    * @return string
    */
    public function getImage() {
        return $this->image;
    }

    /**
    * Sets the image
    *
    * @param string $image
    * @return void
    */
    public function setImage($image) {
        $this->image = $image;
    }
    

}
