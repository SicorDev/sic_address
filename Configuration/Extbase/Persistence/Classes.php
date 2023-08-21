<?php

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2023 SICOR DEVTEAM <dev@sicor-kdl.net>, Sicor KDL GmbH
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

use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Model\Category;
use SICOR\SicAddress\Domain\Model\Content;
use SICOR\SicAddress\Domain\Model\FileReference;
use SICOR\SicAddress\Domain\Service\ConfigurationService;

$mapping = [
    FileReference::class => [
        'tableName' => 'sys_file_reference'
    ],
    Category::class => [
        'tableName' => 'sys_category'
    ],
    Content::class => [
        'tableName' => 'tt_content'
    ]
];

$extensionConfiguration = ConfigurationService::getConfiguration();
if ($extensionConfiguration["ttAddressMapping"]) {
    $mapping[Address::class] = [
        'tableName' => 'tt_address'
    ];
}

return $mapping;
