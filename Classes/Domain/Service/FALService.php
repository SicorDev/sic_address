<?php
namespace SICOR\SicAddress\Domain\Service;

    /***************************************************************
     *
     *  Copyright notice
     *
     *  (c) 2016 SICOR Devteam <dev@sicor-kdl.net>, Sicor KDL
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

/**
 * FAL Service
 */
class FALService {

    protected static $allowedFileExtensions = [
        'image' => ['jpg', 'JPG', 'JPEG', 'jpeg', 'gif', 'GIF', 'png', 'PNG']
    ];

    /**
     *
     * @param array $fileData //File which should be stored
     * @param string $folder //Folder in which the files will be stored under "fileadmin"
     * @param string $className //Classname inserted in sys_file_reference.tablenames
     * @param string $validator //Name of the used validator
     *
     * @return \SICOR\SicAddress\Domain\Model\FileReference
     */
    public static function uploadFalFile($fileData, $folder, $className, $validator = null) {
        if (self::isValidFile($fileData, $validator) && !empty($fileData['name'])) {
            $storageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\StorageRepository::class);
            $newFileReference = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SICOR\SicAddress\Domain\Model\FileReference::class);

            //Check if storage exists and get it, otherwise create it
            $storage = $storageRepository->findByUid(1); #Fileadmin = 1
            $saveFolder = ($storage->hasFolder('./' . $folder)) ? $storage->getFolder('./' . $folder) : $storage->createFolder($folder);

            $fileObject = $storage->addFile($fileData['tmp_name'], $saveFolder, $fileData['name']);
            $newFileReference->setOriginalResource($fileObject);
            $newFileReference->setTablenames($className);

            return $newFileReference;
        }
        return null;
    }

    /**
     * Check if the uploaded file has a allowed fileExtension
     *
     * @param $fileData
     * @param $type
     *
     * @return boolean
     */
    private static function isValidFile($fileData, $type) {
        $fileExtension = explode('.', $fileData['name'])[1];
        $fileType = explode('/', $fileData['type'])[0];

        //No type given => No validation
        if (!$type)
            return true;

        return (in_array($fileExtension, self::$allowedFileExtensions[$type]) && $fileType == $type);
    }
}
