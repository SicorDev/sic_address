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
 * Determines the coordinates (latitude/longitude) of a given address, using the "Google Maps Geocoding API".
 *
 * If you provide a key, it needs to be the "Server Key", not the "API Key" as this is a server-side process.
 * https://developers.google.com/maps/documentation/geocoding/get-api-key
 *
 * @package geolocations
 */
class GeocodeService
{

    /**
     *
     * @var integer
     */
    protected $cacheTime = 7776000;

    /**
     * Base URL to fetch the coordinates from (latitude, longitude of a address string).
     *
     * @var string
     */
    protected $geocodingUrl = '//maps.googleapis.com/maps/api/geocode/json';

    /**
     * Languages provided by google: https://developers.google.com/maps/faq?hl=de#languagesupport
     *
     * @var array
     */
    protected $availableLanguages = [
        'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'en', 'en-AU', 'en-GB', 'es', 'eu', 'eu',
        'fa', 'fi', 'fil', 'fr', 'gl', 'gu', 'hi', 'hr', 'hu', 'id', 'it', 'iw', 'ja', 'kn', 'ko',
        'lt', 'lv', 'ml', 'mr', 'nl', 'no', 'pl', 'pt', 'pt-BR', 'pt-PT', 'ro', 'ru', 'sk', 'sl',
        'sr', 'sv', 'ta', 'te', 'th', 'tl', 'tr', 'uk', 'vi', 'zh-CN', 'zh-TW'
    ];

    /**
     * Sets the google maps API-key and the language.
     *
     * @param string $appendParameter URL-parameter which will be appended to the geocoding-URL
     * @param string $serverKey The server-key to access the google-service, if empty, the default from the configuration is taken
     * @param string $languageKey Language of the result
     * @param integer $cacheTime Sets the caching time of a geocoding result
     * @return void
     */
    public function __construct($appendParameter = null, $serverKey = null, $languageKey = null, $cacheTime = null)
    {
        /*
          if (GeneralUtility::getIndpEnv('TYPO3_SSL')) {
          $protocol = 'https:';
          } else {
          $protocol = 'http:';
          }
         */
        // Get extensions configuration
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geolocations']);
        // Append server-key as URL-parameter
        $key = $serverKey ? $serverKey : (!empty($extConf['serverKey']) ? $extConf['serverKey'] : null);
        if ($key) {
            $urlParameter[] = 'key=' . preg_replace('/[^\da-z_-]/i', '', $key);
        }
        // Append language-key as URL-parameter
        $language = $languageKey ? $languageKey : $GLOBALS['BE_USER']->uc['lang'];
        if ($language && in_array($language, $this->availableLanguages)) {
            $urlParameter[] = 'language=' . $language;
        }
        // Append additional URL-parameters
        if ($appendParameter) {
            $urlParameter[] = preg_replace('/[^\[\]\da-z&_-]/i', '', $appendParameter);
        }
        $this->geocodingUrl = (GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http') . ':' . $this->geocodingUrl . '?';
        if (count($urlParameter) > 0) {
            $this->geocodingUrl .= implode('&', $urlParameter);
        }
        // Overwrite cachetime
        if ($cacheTime) {
            $this->cacheTime = intval($cacheTime);
        }
    }

    /**
     * Reverse geocode a given address to coordinates
     *
     * @param string $address
     * @return array an array with latitude, longitude and place_id
     */
    public function getCoordinatesForAddress($address = '')
    {
        if(!$address)
            return null;

        // Append address to geocoding-url
        $geocodingUrl = $this->geocodingUrl . '&address=' . urlencode($address);
        // Query geocoding-service
        $results = json_decode(GeneralUtility::getUrl($geocodingUrl));
        if ($results->status === 'OK')
        {
            $record = $results->results[0];
            $results = [
                'latitude' => $record->geometry->location->lat,
                'longitude' => $record->geometry->location->lng,
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
