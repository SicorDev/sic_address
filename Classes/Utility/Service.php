<?php
namespace SICOR\SicAddress\Utility;

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
class Service implements SingletonInterface
{
    protected ConfigurationManager $configurationManager;
    protected AddressRepository $addressRepository;

    public function __construct(
        ConfigurationManager $configurationManager,
        AddressRepository $addressRepository
    )
    {
        $this->configurationManager = $configurationManager;
        $this->addressRepository = $addressRepository;
    }

    public function getPluginSettings(): array
    {
        return $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, null, 'tx_sicaddress_sicaddress');
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function getCenterAddressObjectFromFlexConfig(): ?Address
    {
        $centerAddressSetting = $this->getPluginSettings()['centerAddress'];
        if($centerAddressSetting) {
            $arr = explode('_', $centerAddressSetting);
            $centerAddressUid = array_pop($arr);

            return $this->addressRepository->findByUid($centerAddressUid);
        }

        return new Address();
    }

    public static function startsWith($haystack, $needle): bool
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public static function getConfiguration(): array
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('sic_address');
    }
}
