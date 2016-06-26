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
    protected $title = '';
    
    /**
     * tcaLabel
     *
     * @var string
     */
    protected $tcaLabel = '';
    
    /**
     * tcaOverride
     *
     * @var string
     */
    protected $tcaOverride = '';


    /**
     * dbOverride
     *
     * @var string
     */
    protected $dbOverride = '';
    
    /**
     * settings
     *
     * @var string
     */
    protected $settings = '';
    
    /**
     * type
     *
     * @var string
     */
    protected $type = '';
    
    /**
     * isListLabel
     *
     * @var bool
     */
    protected $isListLabel = false;
    
    /**
     * DomainProperty constructor.
     * @param string $title
     * @param \SICOR\SicAddress\Domain\Model\AbstractFieldType $type
     * @param string $label
     * @param string $tcaOverride
     * @param string $dbOverride
     * @param string $settings
     * @param bool $isListLabel
     */
    public function setProperties($title, \SICOR\SicAddress\Domain\Model\AbstractFieldType $type, $label, $tcaOverride, $dbOverride, $settings, $isListLabel)
    {
        $this->title = $title;
        $this->type = $type;
        $this->label = $label;
        $this->tcaOverride = $tcaOverride;
        $this->dbOverride = $dbOverride;
        $this->settings = $settings;
        $this->isListLabel = $isListLabel;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Returns the tcaLabel
     *
     * @return string $tcaLabel
     */
    public function getTcaLabel()
    {
        return $this->tcaLabel;
    }
    
    /**
     * Sets the tcaLabel
     *
     * @param string $tcaLabel
     * @return void
     */
    public function setTcaLabel($tcaLabel)
    {
        $this->tcaLabel = $tcaLabel;
    }
    
    /**
     * Returns the tcaOverride
     *
     * @return string $tcaOverride
     */
    public function getTcaOverride()
    {
        return $this->tcaOverride;
    }
    
    /**
     * Sets the tcaOverride
     *
     * @param string $tcaOverride
     * @return void
     */
    public function setTcaOverride($tcaOverride)
    {
        $this->tcaOverride = $tcaOverride;
    }

    /**
     * Returns the $label
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Sets the label
     *
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Returns the $dbOverride
     *
     * @return string
     */
    public function getDbOverride() {
        return $this->dbOverride;
    }

    /**
     * Sets the dbOverride
     *
     * @param string $dbOverride
     */
    public function setDbOverride($dbOverride) {
        $this->dbOverride = $dbOverride;
    }

    
    /**
     * Returns the settings
     *
     * @return string $settings
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
    /**
     * Sets the settings
     *
     * @param string $settings
     * @return void
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }
    
    
    /**
     * Returns the isListLabel
     *
     * @return bool $isListLabel
     */
    public function getIsListLabel()
    {
        return $this->isListLabel;
    }
    
    /**
     * Sets the isListLabel
     *
     * @param bool $isListLabel
     * @return void
     */
    public function setIsListLabel($isListLabel)
    {
        $this->isListLabel = $isListLabel;
    }

}