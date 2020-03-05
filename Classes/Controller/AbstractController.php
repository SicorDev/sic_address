<?php
namespace SICOR\SicAddress\Controller;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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

    /**
     * @param ViewInterface $view
     */
    public function initializeView(ViewInterface $view)
    {
        parent::initializeView($view); // TODO: Change the autogenerated stub

        if(! in_array($this->request->getControllerActionName(), array('create', 'update'))) {

            $updateOrFirstInstall = \file_exists(PATH_typo3conf . 'ext/sic_address/PLEASE_GENERATE');
            if($updateOrFirstInstall && $this->domainPropertyRepository && $this->domainPropertyRepository->countAll()) {
                $this->addFlashMessage(
                    $this->translate('label_update_message'),
                    $this->translate('label_update_title'),
                    AbstractMessage::ERROR
                );
            }
        }
    }

}
