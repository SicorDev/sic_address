<?php
namespace SICOR\SicAddress\Controller;

class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


    /**
     * Returns translation for given key (and extension)
     *
     * @param [type] $key
     * @param string $extension
     * @return string
     */
    protected function translate($key, $extension = 'sic_address') {
        return LocalizationUtility::translate($key, $extension );
    }

    }

}
