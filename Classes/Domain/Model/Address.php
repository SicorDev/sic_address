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
    * gender
    *
    * @var string
    */
    protected $gender;
    
    /**
    * name
    *
    * @var string
    */
    protected $name;
    
    /**
    * first_name
    *
    * @var string
    */
    protected $first_name;
    
    /**
    * middle_name
    *
    * @var string
    */
    protected $middle_name;
    
    /**
    * last_name
    *
    * @var string
    */
    protected $last_name;
    
    /**
    * birthday
    *
    * @var string
    */
    protected $birthday;
    
    /**
    * title
    *
    * @var string
    */
    protected $title;
    
    /**
    * email
    *
    * @var string
    */
    protected $email;
    
    /**
    * phone
    *
    * @var string
    */
    protected $phone;
    
    /**
    * mobile
    *
    * @var string
    */
    protected $mobile;
    
    /**
    * www
    *
    * @var string
    */
    protected $www;
    
    /**
    * address
    *
    * @var string
    */
    protected $address;
    
    /**
    * building
    *
    * @var string
    */
    protected $building;
    
    /**
    * room
    *
    * @var string
    */
    protected $room;
    
    /**
    * company
    *
    * @var string
    */
    protected $company;
    
    /**
    * position
    *
    * @var string
    */
    protected $position;
    
    /**
    * city
    *
    * @var string
    */
    protected $city;
    
    /**
    * zip
    *
    * @var string
    */
    protected $zip;
    
    /**
    * region
    *
    * @var string
    */
    protected $region;
    
    /**
    * country
    *
    * @var string
    */
    protected $country;
    
    /**
    * image
    *
    * @var string
    */
    protected $image;
    
    /**
    * fax
    *
    * @var string
    */
    protected $fax;
    
    /**
    * description
    *
    * @var string
    */
    protected $description;
    
    /**
    * skype
    *
    * @var string
    */
    protected $skype;
    
    /**
    * twitter
    *
    * @var string
    */
    protected $twitter;
    
    /**
    * facebook
    *
    * @var string
    */
    protected $facebook;
    
    /**
    * linkedin
    *
    * @var string
    */
    protected $linkedin;
    
    /**
    * categories
    *
    * @var string
    */
    protected $categories;
    
    /**
    * latitude
    *
    * @var string
    */
    protected $latitude;
    
    /**
    * longitude
    *
    * @var string
    */
    protected $longitude;
    
    /**
    * hans
    *
    * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
    */
    protected $hans;
    

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
    * Returns the gender
    *
    * @return string
    */
    public function getGender() {
        return $this->gender;
    }

    /**
    * Sets the gender
    *
    * @param string $gender
    * @return void
    */
    public function setGender($gender) {
        $this->gender = $gender;
    }
    
    /**
    * Returns the name
    *
    * @return string
    */
    public function getName() {
        return $this->name;
    }

    /**
    * Sets the name
    *
    * @param string $name
    * @return void
    */
    public function setName($name) {
        $this->name = $name;
    }
    
    /**
    * Returns the first_name
    *
    * @return string
    */
    public function getFirst_name() {
        return $this->first_name;
    }

    /**
    * Sets the first_name
    *
    * @param string $first_name
    * @return void
    */
    public function setFirst_name($first_name) {
        $this->first_name = $first_name;
    }
    
    /**
    * Returns the middle_name
    *
    * @return string
    */
    public function getMiddle_name() {
        return $this->middle_name;
    }

    /**
    * Sets the middle_name
    *
    * @param string $middle_name
    * @return void
    */
    public function setMiddle_name($middle_name) {
        $this->middle_name = $middle_name;
    }
    
    /**
    * Returns the last_name
    *
    * @return string
    */
    public function getLast_name() {
        return $this->last_name;
    }

    /**
    * Sets the last_name
    *
    * @param string $last_name
    * @return void
    */
    public function setLast_name($last_name) {
        $this->last_name = $last_name;
    }
    
    /**
    * Returns the birthday
    *
    * @return string
    */
    public function getBirthday() {
        return $this->birthday;
    }

    /**
    * Sets the birthday
    *
    * @param string $birthday
    * @return void
    */
    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }
    
    /**
    * Returns the title
    *
    * @return string
    */
    public function getTitle() {
        return $this->title;
    }

    /**
    * Sets the title
    *
    * @param string $title
    * @return void
    */
    public function setTitle($title) {
        $this->title = $title;
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
    * Returns the phone
    *
    * @return string
    */
    public function getPhone() {
        return $this->phone;
    }

    /**
    * Sets the phone
    *
    * @param string $phone
    * @return void
    */
    public function setPhone($phone) {
        $this->phone = $phone;
    }
    
    /**
    * Returns the mobile
    *
    * @return string
    */
    public function getMobile() {
        return $this->mobile;
    }

    /**
    * Sets the mobile
    *
    * @param string $mobile
    * @return void
    */
    public function setMobile($mobile) {
        $this->mobile = $mobile;
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
    * Returns the address
    *
    * @return string
    */
    public function getAddress() {
        return $this->address;
    }

    /**
    * Sets the address
    *
    * @param string $address
    * @return void
    */
    public function setAddress($address) {
        $this->address = $address;
    }
    
    /**
    * Returns the building
    *
    * @return string
    */
    public function getBuilding() {
        return $this->building;
    }

    /**
    * Sets the building
    *
    * @param string $building
    * @return void
    */
    public function setBuilding($building) {
        $this->building = $building;
    }
    
    /**
    * Returns the room
    *
    * @return string
    */
    public function getRoom() {
        return $this->room;
    }

    /**
    * Sets the room
    *
    * @param string $room
    * @return void
    */
    public function setRoom($room) {
        $this->room = $room;
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
    * Returns the position
    *
    * @return string
    */
    public function getPosition() {
        return $this->position;
    }

    /**
    * Sets the position
    *
    * @param string $position
    * @return void
    */
    public function setPosition($position) {
        $this->position = $position;
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
    * Returns the zip
    *
    * @return string
    */
    public function getZip() {
        return $this->zip;
    }

    /**
    * Sets the zip
    *
    * @param string $zip
    * @return void
    */
    public function setZip($zip) {
        $this->zip = $zip;
    }
    
    /**
    * Returns the region
    *
    * @return string
    */
    public function getRegion() {
        return $this->region;
    }

    /**
    * Sets the region
    *
    * @param string $region
    * @return void
    */
    public function setRegion($region) {
        $this->region = $region;
    }
    
    /**
    * Returns the country
    *
    * @return string
    */
    public function getCountry() {
        return $this->country;
    }

    /**
    * Sets the country
    *
    * @param string $country
    * @return void
    */
    public function setCountry($country) {
        $this->country = $country;
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
    * Returns the description
    *
    * @return string
    */
    public function getDescription() {
        return $this->description;
    }

    /**
    * Sets the description
    *
    * @param string $description
    * @return void
    */
    public function setDescription($description) {
        $this->description = $description;
    }
    
    /**
    * Returns the skype
    *
    * @return string
    */
    public function getSkype() {
        return $this->skype;
    }

    /**
    * Sets the skype
    *
    * @param string $skype
    * @return void
    */
    public function setSkype($skype) {
        $this->skype = $skype;
    }
    
    /**
    * Returns the twitter
    *
    * @return string
    */
    public function getTwitter() {
        return $this->twitter;
    }

    /**
    * Sets the twitter
    *
    * @param string $twitter
    * @return void
    */
    public function setTwitter($twitter) {
        $this->twitter = $twitter;
    }
    
    /**
    * Returns the facebook
    *
    * @return string
    */
    public function getFacebook() {
        return $this->facebook;
    }

    /**
    * Sets the facebook
    *
    * @param string $facebook
    * @return void
    */
    public function setFacebook($facebook) {
        $this->facebook = $facebook;
    }
    
    /**
    * Returns the linkedin
    *
    * @return string
    */
    public function getLinkedin() {
        return $this->linkedin;
    }

    /**
    * Sets the linkedin
    *
    * @param string $linkedin
    * @return void
    */
    public function setLinkedin($linkedin) {
        $this->linkedin = $linkedin;
    }
    
    /**
    * Returns the categories
    *
    * @return string
    */
    public function getCategories() {
        return $this->categories;
    }

    /**
    * Sets the categories
    *
    * @param string $categories
    * @return void
    */
    public function setCategories($categories) {
        $this->categories = $categories;
    }
    
    /**
    * Returns the latitude
    *
    * @return string
    */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
    * Sets the latitude
    *
    * @param string $latitude
    * @return void
    */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }
    
    /**
    * Returns the longitude
    *
    * @return string
    */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
    * Sets the longitude
    *
    * @param string $longitude
    * @return void
    */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }
    
    /**
    * Returns the hans
    *
    * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
    */
    public function getHans() {
        return $this->hans;
    }

    /**
    * Sets the hans
    *
    * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $hans
    * @return void
    */
    public function setHans($hans) {
        $this->hans = $hans;
    }
    

}
