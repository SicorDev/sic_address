<?php
namespace SICOR\SicAddress\Domain\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Determines the coordinates (latitude/longitude) of a given address, using Open Street Map.
 */
class GeocodeService
{
    /**
     * Get GPS coordinates for given address
     *
     * @param string $address
     * @return array an array with latitude, longitude and place_id
     */
    public function getCoordinatesForAddress($address)
    {
        if (empty($address))
            return null;

        $geocodingParams = '&q=' . urlencode($address);

        return $this->getCoordinatesFromParams($geocodingParams);
    }

    /**
     * Get GPS coordinates for postal code
     *
     * @param string $postalcode
     * @param string $country
     * @return array an array with latitude, longitude and place_id
     */
    public function getCoordinatesForPostalCode($postalcode='', $country='')
    {
        $geocodingParams = '&postalcode='.$postalcode.'&country='.$country;
        return $this->getCoordinatesFromParams($geocodingParams);
    }

    /**
     * Get GPS coordinates from given params
     *
     * @param string $geocodingParams
     * @return array an array with latitude, longitude and place_id
     */
    public function getCoordinatesFromParams($geocodingParams)
    {
        $geocodingUrl = 'https://nominatim.openstreetmap.org/search?format=json'.$geocodingParams;

        // Query geocoding-service
        $results = json_decode(GeneralUtility::getUrl($geocodingUrl, 0, array('Content-Type: application/json', 'Accept: application/json', 'User-Agent: https://extensions.typo3.org/extension/sic_address')));

        if (!empty($results[0]->lat))
        {
            $record = $results[0];
            $results = [
                'latitude' => $record->lat,
                'longitude' => $record->lon,
                'place_id' => $record->place_id ? $record->place_id : ''
            ];
        }
        else
        {
            $results = null;
        }

        return $results;
    }
}
