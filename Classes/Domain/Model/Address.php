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
    * tx_siccomcal_event_location
    *
    * @var string
    */
    protected $tx_siccomcal_event_location;
    
    /**
    * name
    *
    * @var string
    */
    protected $name;
    
    /**
    * gender
    *
    * @var string
    */
    protected $gender;
    
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
    * addressgroup
    *
    * @var string
    */
    protected $addressgroup;
    
    /**
    * tx_spdirectory_cat
    *
    * @var string
    */
    protected $tx_spdirectory_cat;
    
    /**
    * tx_spdirectory_fegroup
    *
    * @var string
    */
    protected $tx_spdirectory_fegroup;
    
    /**
    * tx_spdirectory_feuser
    *
    * @var string
    */
    protected $tx_spdirectory_feuser;
    
    /**
    * tx_rggooglemap_lng
    *
    * @var string
    */
    protected $tx_rggooglemap_lng;
    
    /**
    * tx_rggooglemap_lat
    *
    * @var string
    */
    protected $tx_rggooglemap_lat;
    
    /**
    * tx_rggooglemap_display
    *
    * @var string
    */
    protected $tx_rggooglemap_display;
    
    /**
    * tx_rggooglemap_cat
    *
    * @var string
    */
    protected $tx_rggooglemap_cat;
    
    /**
    * tx_rggooglemap_tab
    *
    * @var string
    */
    protected $tx_rggooglemap_tab;
    
    /**
    * tx_rggooglemap_cat2
    *
    * @var string
    */
    protected $tx_rggooglemap_cat2;
    
    /**
    * tx_rggooglemap_ce
    *
    * @var string
    */
    protected $tx_rggooglemap_ce;
    
    /**
    * tx_msitesymstylespre_style
    *
    * @var string
    */
    protected $tx_msitesymstylespre_style;
    
    /**
    * tx_msitegis_firstname
    *
    * @var string
    */
    protected $tx_msitegis_firstname;
    
    /**
    * tx_msitegis_gis_x
    *
    * @var string
    */
    protected $tx_msitegis_gis_x;
    
    /**
    * tx_msitegis_gis_y
    *
    * @var string
    */
    protected $tx_msitegis_gis_y;
    
    /**
    * tx_msitegis_zentroid
    *
    * @var string
    */
    protected $tx_msitegis_zentroid;
    
    /**
    * tx_msitegis_geometry
    *
    * @var string
    */
    protected $tx_msitegis_geometry;
    
    /**
    * tx_msitegis_gis_q
    *
    * @var string
    */
    protected $tx_msitegis_gis_q;
    
    /**
    * tx_rggooglemap_q
    *
    * @var string
    */
    protected $tx_rggooglemap_q;
    

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
    * Returns the tx_siccomcal_event_location
    *
    * @return string
    */
    public function getTx_siccomcal_event_location() {
        return $this->tx_siccomcal_event_location;
    }

    /**
    * Sets the tx_siccomcal_event_location
    *
    * @param string $tx_siccomcal_event_location
    * @return void
    */
    public function setTx_siccomcal_event_location($tx_siccomcal_event_location) {
        $this->tx_siccomcal_event_location = $tx_siccomcal_event_location;
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
    * Returns the addressgroup
    *
    * @return string
    */
    public function getAddressgroup() {
        return $this->addressgroup;
    }

    /**
    * Sets the addressgroup
    *
    * @param string $addressgroup
    * @return void
    */
    public function setAddressgroup($addressgroup) {
        $this->addressgroup = $addressgroup;
    }
    
    /**
    * Returns the tx_spdirectory_cat
    *
    * @return string
    */
    public function getTx_spdirectory_cat() {
        return $this->tx_spdirectory_cat;
    }

    /**
    * Sets the tx_spdirectory_cat
    *
    * @param string $tx_spdirectory_cat
    * @return void
    */
    public function setTx_spdirectory_cat($tx_spdirectory_cat) {
        $this->tx_spdirectory_cat = $tx_spdirectory_cat;
    }
    
    /**
    * Returns the tx_spdirectory_fegroup
    *
    * @return string
    */
    public function getTx_spdirectory_fegroup() {
        return $this->tx_spdirectory_fegroup;
    }

    /**
    * Sets the tx_spdirectory_fegroup
    *
    * @param string $tx_spdirectory_fegroup
    * @return void
    */
    public function setTx_spdirectory_fegroup($tx_spdirectory_fegroup) {
        $this->tx_spdirectory_fegroup = $tx_spdirectory_fegroup;
    }
    
    /**
    * Returns the tx_spdirectory_feuser
    *
    * @return string
    */
    public function getTx_spdirectory_feuser() {
        return $this->tx_spdirectory_feuser;
    }

    /**
    * Sets the tx_spdirectory_feuser
    *
    * @param string $tx_spdirectory_feuser
    * @return void
    */
    public function setTx_spdirectory_feuser($tx_spdirectory_feuser) {
        $this->tx_spdirectory_feuser = $tx_spdirectory_feuser;
    }
    
    /**
    * Returns the tx_rggooglemap_lng
    *
    * @return string
    */
    public function getTx_rggooglemap_lng() {
        return $this->tx_rggooglemap_lng;
    }

    /**
    * Sets the tx_rggooglemap_lng
    *
    * @param string $tx_rggooglemap_lng
    * @return void
    */
    public function setTx_rggooglemap_lng($tx_rggooglemap_lng) {
        $this->tx_rggooglemap_lng = $tx_rggooglemap_lng;
    }
    
    /**
    * Returns the tx_rggooglemap_lat
    *
    * @return string
    */
    public function getTx_rggooglemap_lat() {
        return $this->tx_rggooglemap_lat;
    }

    /**
    * Sets the tx_rggooglemap_lat
    *
    * @param string $tx_rggooglemap_lat
    * @return void
    */
    public function setTx_rggooglemap_lat($tx_rggooglemap_lat) {
        $this->tx_rggooglemap_lat = $tx_rggooglemap_lat;
    }
    
    /**
    * Returns the tx_rggooglemap_display
    *
    * @return string
    */
    public function getTx_rggooglemap_display() {
        return $this->tx_rggooglemap_display;
    }

    /**
    * Sets the tx_rggooglemap_display
    *
    * @param string $tx_rggooglemap_display
    * @return void
    */
    public function setTx_rggooglemap_display($tx_rggooglemap_display) {
        $this->tx_rggooglemap_display = $tx_rggooglemap_display;
    }
    
    /**
    * Returns the tx_rggooglemap_cat
    *
    * @return string
    */
    public function getTx_rggooglemap_cat() {
        return $this->tx_rggooglemap_cat;
    }

    /**
    * Sets the tx_rggooglemap_cat
    *
    * @param string $tx_rggooglemap_cat
    * @return void
    */
    public function setTx_rggooglemap_cat($tx_rggooglemap_cat) {
        $this->tx_rggooglemap_cat = $tx_rggooglemap_cat;
    }
    
    /**
    * Returns the tx_rggooglemap_tab
    *
    * @return string
    */
    public function getTx_rggooglemap_tab() {
        return $this->tx_rggooglemap_tab;
    }

    /**
    * Sets the tx_rggooglemap_tab
    *
    * @param string $tx_rggooglemap_tab
    * @return void
    */
    public function setTx_rggooglemap_tab($tx_rggooglemap_tab) {
        $this->tx_rggooglemap_tab = $tx_rggooglemap_tab;
    }
    
    /**
    * Returns the tx_rggooglemap_cat2
    *
    * @return string
    */
    public function getTx_rggooglemap_cat2() {
        return $this->tx_rggooglemap_cat2;
    }

    /**
    * Sets the tx_rggooglemap_cat2
    *
    * @param string $tx_rggooglemap_cat2
    * @return void
    */
    public function setTx_rggooglemap_cat2($tx_rggooglemap_cat2) {
        $this->tx_rggooglemap_cat2 = $tx_rggooglemap_cat2;
    }
    
    /**
    * Returns the tx_rggooglemap_ce
    *
    * @return string
    */
    public function getTx_rggooglemap_ce() {
        return $this->tx_rggooglemap_ce;
    }

    /**
    * Sets the tx_rggooglemap_ce
    *
    * @param string $tx_rggooglemap_ce
    * @return void
    */
    public function setTx_rggooglemap_ce($tx_rggooglemap_ce) {
        $this->tx_rggooglemap_ce = $tx_rggooglemap_ce;
    }
    
    /**
    * Returns the tx_msitesymstylespre_style
    *
    * @return string
    */
    public function getTx_msitesymstylespre_style() {
        return $this->tx_msitesymstylespre_style;
    }

    /**
    * Sets the tx_msitesymstylespre_style
    *
    * @param string $tx_msitesymstylespre_style
    * @return void
    */
    public function setTx_msitesymstylespre_style($tx_msitesymstylespre_style) {
        $this->tx_msitesymstylespre_style = $tx_msitesymstylespre_style;
    }
    
    /**
    * Returns the tx_msitegis_firstname
    *
    * @return string
    */
    public function getTx_msitegis_firstname() {
        return $this->tx_msitegis_firstname;
    }

    /**
    * Sets the tx_msitegis_firstname
    *
    * @param string $tx_msitegis_firstname
    * @return void
    */
    public function setTx_msitegis_firstname($tx_msitegis_firstname) {
        $this->tx_msitegis_firstname = $tx_msitegis_firstname;
    }
    
    /**
    * Returns the tx_msitegis_gis_x
    *
    * @return string
    */
    public function getTx_msitegis_gis_x() {
        return $this->tx_msitegis_gis_x;
    }

    /**
    * Sets the tx_msitegis_gis_x
    *
    * @param string $tx_msitegis_gis_x
    * @return void
    */
    public function setTx_msitegis_gis_x($tx_msitegis_gis_x) {
        $this->tx_msitegis_gis_x = $tx_msitegis_gis_x;
    }
    
    /**
    * Returns the tx_msitegis_gis_y
    *
    * @return string
    */
    public function getTx_msitegis_gis_y() {
        return $this->tx_msitegis_gis_y;
    }

    /**
    * Sets the tx_msitegis_gis_y
    *
    * @param string $tx_msitegis_gis_y
    * @return void
    */
    public function setTx_msitegis_gis_y($tx_msitegis_gis_y) {
        $this->tx_msitegis_gis_y = $tx_msitegis_gis_y;
    }
    
    /**
    * Returns the tx_msitegis_zentroid
    *
    * @return string
    */
    public function getTx_msitegis_zentroid() {
        return $this->tx_msitegis_zentroid;
    }

    /**
    * Sets the tx_msitegis_zentroid
    *
    * @param string $tx_msitegis_zentroid
    * @return void
    */
    public function setTx_msitegis_zentroid($tx_msitegis_zentroid) {
        $this->tx_msitegis_zentroid = $tx_msitegis_zentroid;
    }
    
    /**
    * Returns the tx_msitegis_geometry
    *
    * @return string
    */
    public function getTx_msitegis_geometry() {
        return $this->tx_msitegis_geometry;
    }

    /**
    * Sets the tx_msitegis_geometry
    *
    * @param string $tx_msitegis_geometry
    * @return void
    */
    public function setTx_msitegis_geometry($tx_msitegis_geometry) {
        $this->tx_msitegis_geometry = $tx_msitegis_geometry;
    }
    
    /**
    * Returns the tx_msitegis_gis_q
    *
    * @return string
    */
    public function getTx_msitegis_gis_q() {
        return $this->tx_msitegis_gis_q;
    }

    /**
    * Sets the tx_msitegis_gis_q
    *
    * @param string $tx_msitegis_gis_q
    * @return void
    */
    public function setTx_msitegis_gis_q($tx_msitegis_gis_q) {
        $this->tx_msitegis_gis_q = $tx_msitegis_gis_q;
    }
    
    /**
    * Returns the tx_rggooglemap_q
    *
    * @return string
    */
    public function getTx_rggooglemap_q() {
        return $this->tx_rggooglemap_q;
    }

    /**
    * Sets the tx_rggooglemap_q
    *
    * @param string $tx_rggooglemap_q
    * @return void
    */
    public function setTx_rggooglemap_q($tx_rggooglemap_q) {
        $this->tx_rggooglemap_q = $tx_rggooglemap_q;
    }
    

}
