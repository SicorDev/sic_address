<?php

use SICOR\SicAddress\Utility\Service;

$mapping = [
    \SICOR\SicAddress\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference'
    ],
    \SICOR\SicAddress\Domain\Model\Category::class => [
        'tableName' => 'sys_category'
    ],
    \SICOR\SicAddress\Domain\Model\Content::class => [
        'tableName' => 'tt_content'
    ]
];

$extensionConfiguration = Service::getConfiguration();
if ($extensionConfiguration["ttAddressMapping"]) {
    $mapping[SICOR\SicAddress\Domain\Model\Address::class] = [
        'tableName' => 'tt_address'
    ];
}

return $mapping;
