<?php

use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Model\Category;
use SICOR\SicAddress\Domain\Model\Content;
use SICOR\SicAddress\Domain\Model\FileReference;
use SICOR\SicAddress\Utility\Service;

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

$extensionConfiguration = Service::getConfiguration();
if ($extensionConfiguration["ttAddressMapping"]) {
    $mapping[Address::class] = [
        'tableName' => 'tt_address'
    ];
}

return $mapping;
