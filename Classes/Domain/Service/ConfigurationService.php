<?php

namespace SICOR\SicAddress\Domain\Service;

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
use SICOR\SicAddress\Domain\Repository\AddressRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use function array_pop;
use function explode;

/**
 * Service
 */
class ConfigurationService implements SingletonInterface
{
    protected ConfigurationManager $configurationManager;
    protected AddressRepository $addressRepository;

    public function __construct(
        ConfigurationManager $configurationManager,
        AddressRepository    $addressRepository
    )
    {
        $this->configurationManager = $configurationManager;
        $this->addressRepository = $addressRepository;
    }

    public function getCenterAddressObjectFromFlexConfig(): ?Address
    {
        $flexSettings = $this->getPluginSettings();
        if(!array_key_exists('centerAddress', $flexSettings)) {
            return null;
        }

        $centerAddressSetting = $this->getPluginSettings()['centerAddress'];
        if ($centerAddressSetting) {
            $arr = explode('_', $centerAddressSetting);
            $centerAddressUid = array_pop($arr);
            return $this->addressRepository->findByUid($centerAddressUid);
        }

        return null;
    }

    public function getPluginSettings(): array
    {
        return $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, null, 'tx_sicaddress_sicaddress');
    }

    public static function getConfiguration(): array
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('sic_address');
    }
}
