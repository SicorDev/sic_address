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
 * DomainProperty
 */
class DomainProperty extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = "";

    /**
     * type
     *
     * @var SICOR\SicAddress\Domain\Model\FieldType
     */
    protected $type = "";

    /**
     * label
     *
     * @var string
     */
    protected $label = "";

    /**
     * tcaOverride
     *
     * @var string
     */
    protected $tcaOverride;

    /**
     * Settings
     *
     * @var string
     */
    protected $settings;

    /**
     * TCA list label
     *
     * @var boolean
     */
    protected $isListLabel;

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return SICOR\SicAddress\Domain\Model\FieldType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param SICOR\SicAddress\Domain\Model\FieldType $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getTcaOverride() {
        return $this->tcaOverride;
    }

    /**
     * @param string $tcaOverride
     */
    public function setTcaOverride($tcaOverride) {
        $this->tcaOverride = $tcaOverride;
    }

    /**
     * @return string
     */
    public function getSettings() {
        return $this->settings;
    }

    /**
     * @param string $settings
     */
    public function setSettings($settings) {
        $this->settings = $settings;
    }

    /**
     * @return boolean
     */
    public function isIsListLabel() {
        return $this->isListLabel;
    }

    /**
     * @param boolean $isListLabel
     */
    public function setIsListLabel($isListLabel) {
        $this->isListLabel = $isListLabel;
    }

}