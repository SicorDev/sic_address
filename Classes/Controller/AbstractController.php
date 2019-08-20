<?php
namespace SICOR\SicAddress\Controller;

class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * UReturns translation for given key (and extension)
     *
     * @param [type] $key
     * @param string $extension
     * @return void
     */
    protected function translate($key, $extension = 'sic_address') {
        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, $extension );
    }

}