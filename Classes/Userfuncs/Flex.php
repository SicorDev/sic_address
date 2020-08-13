<?php

namespace SICOR\SicAddress\Userfuncs;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2018 DEV Team <dev@sicor-kdl.net>, SICOR KDL GmbH
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

use SICOR\SicAddress\Utility\Service;

class Flex
{
    /**
     * Holds the Extension configuration
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * Flex constructor.
     */
    public function __construct()
    {
        $this->extensionConfiguration = Service::getConfiguration();
    }

    /**
     * @return bool
     */
    public function useTTAddress()
    {
        return $this->extensionConfiguration['ttAddressMapping'];
    }

    /**
     * @return bool
     */
    public function useSicAddress()
    {
        return !$this->extensionConfiguration['ttAddressMapping'];
    }
}
