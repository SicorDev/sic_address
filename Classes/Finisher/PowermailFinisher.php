<?php

namespace SICOR\SicAddress\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Finisher\AbstractFinisher;
use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Repository\AddressRepository;
use SICOR\SicAddress\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Throwable;
use function array_key_exists;

class PowermailFinisher extends AbstractFinisher
{
    protected Mail $mail;
    protected array $settings;
    protected array $extensionConfiguration;

    public function initializeFinisher(): void
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $this->extensionConfiguration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'sic_address'
        );
    }

    public function myFinisher(): void
    {
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $addressRepository = GeneralUtility::makeInstance(addressRepository::class);
        $categoryRepository = GeneralUtility::makeInstance(CategoryRepository::class);

        try {
            // Create Address
            $address = GeneralUtility::makeInstance(Address::class);
            $address->setPid(386);
            $address->setHidden(true);

            // String fields
            $address->setAllgemeinName($this->getValue('namedesbetriebesfirma'));
            $address->setAllgemeinZuchtstation($this->getValue('namenszusatz'));
            $address->setAllgemeinAdresse($this->getValue('strassenr'));
            $address->setAllgemeinPlz($this->getValue('plz'));
            $address->setAllgemeinOrt($this->getValue('ort'));
            $address->setAllgemeinWebseite($this->getValue('website'));
            $address->setAllgemeinLinkedin($this->getValue('linkedin'));
            $address->setAllgemeinInstagram($this->getValue('instagram'));
            $address->setAllgemeinYoutubekanal($this->getValue('youtube'));
            $address->setAllgemeinFacebook($this->getValue('facebook'));
            $address->setAllgemeinX($this->getValue('x'));
            $address->setAllgemeinYoutubevideos($this->getValue('youtubevideos'));

            $address->setProfilTitel($this->getValue('titel_01'));
            $address->setProfilBeschreibung($this->getValue('description'));
            $address->setProfilWeitereberufe($this->getValue('weitereausbildungsberufe'));

            $address->setKontaktAnrede($this->getValue('geschlecht'));
            $address->setKontaktTitel($this->getValue('titel'));
            $address->setKontaktVorname($this->getValue('vorname'));
            $address->setKontaktName($this->getValue('name'));
            $address->setKontaktZustandigkeit($this->getValue('abteilungposition'));
            $address->setKontaktTelefon($this->getValue('telefon_ansprech'));
            $address->setKontaktEmail($this->getValue('e_mail_01'));

            $address->setKontakt2Anrede($this->getValue('geschlecht_01'));
            $address->setKontakt2Titel($this->getValue('titel_02'));
            $address->setKontakt2Vorname($this->getValue('vorname_02'));
            $address->setKontakt2Name($this->getValue('name_02'));
            $address->setKontakt2Zustandigkeit($this->getValue('abteilungposition_01'));
            $address->setKontakt2Telefon($this->getValue('telefon_ansprech_01'));
            $address->setKontakt2Email($this->getValue('e_mail_03'));

            $address->setInserentAnrede($this->getValue('geschlecht_02'));
            $address->setInserentTitel($this->getValue('titel_03'));
            $address->setInserentVorname($this->getValue('vorname_01'));
            $address->setInserentNachname($this->getValue('name_01'));
            $address->setInserentPosition($this->getValue('funktionposition'));
            $address->setInserentTelefon($this->getValue('telefon_01'));
            $address->setInserentEmail($this->getValue('e_mail_02'));

            // Add Categories
            $categories = $this->getValue('schwerpunkte');
            $categories = array_merge($categories, $this->getValue('kulturarten'));
            $categories = array_merge($categories, $this->getValue('ausbildungsberufe'));
            foreach ($categories as $category) {
                $address->addCategory($categoryRepository->findByUid($category));
            }

            $addressRepository->add($address);
            $persistenceManager->persistAll();

            // Add Images
            $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
            $storage = $storageRepository->getDefaultStorage();
            if ($storage) {
                $image = $this->getValue('logo')[0];
                $this->createFileReference($storage, $address->getUid(), $address->getPid(), 'tt_address', 'allgemein_logo', $image);

                $image = $this->getValue('fotokontaktperson')[0];
                $this->createFileReference($storage, $address->getUid(), $address->getPid(), 'tt_address', 'kontakt_foto', $image);

                $image = $this->getValue('fotokontaktperson_01')[0];
                $this->createFileReference($storage, $address->getUid(), $address->getPid(), 'tt_address', 'kontakt2_foto', $image);

                foreach ($this->getValue('bilder') as $image) {
                    $this->createFileReference($storage, $address->getUid(), $address->getPid(), 'tt_address', 'allgemein_bilder', $image);
                }
            }

            $address->setSlug($this->generateSlug($address));
            $addressRepository->update($address);
            $persistenceManager->persistAll();
        } catch (Throwable $e) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($e);
            exit(1);
        }
    }

    private function generateSlug(Address $address): string
    {
        $slugHelper = GeneralUtility::makeInstance(
            SlugHelper::class,
            'tt_address',
            'slug',
            $GLOBALS['TCA']['tt_address']['columns']['slug']['config']
        );
        $record = ['title' => $address->getTitle(), 'pid' => $address->getPid(), 'uid' => $address->getUid()];

        $slug = $slugHelper->generate($record, $record['pid']);
        $state = RecordStateFactory::forName('tt_address')->fromArray($record, $record['pid'], $record['uid']);
        return $slugHelper->buildSlugForUniqueInTable($slug, $state);
    }

    private function createFileReference($storage, $record, $pid, $tablenames, $fieldname, $image)
    {
        if (empty($image)) return;
        $file = $storage->getFile('/user_upload/aubildungsbetriebe/' . $image);
        if (!$file) return;
        $fileuid = $file->getUid();

        // Create sys_refindex entry manually
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_refindex');
        $queryBuilder
            ->insert('sys_refindex')
            ->values([
                'hash' => md5($fileuid),
                'tablename' => $tablenames,
                'recuid' => $record,
                'ref_table' => 'sys_file_reference',
                'ref_uid' => $fileuid,
                'field' => $fieldname,
            ])
            ->execute();

        // Create sys_file_reference entry manually
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');
        $queryBuilder
            ->insert('sys_file_reference')
            ->values([
                'pid' => $pid,
                'tstamp' => time(),
                'crdate' => time(),
                'uid_local' => $fileuid,
                'uid_foreign' => $record,
                'tablenames' => $tablenames,
                'fieldname' => $fieldname,
                'table_local' => 'sys_file',
            ])
            ->execute();
    }

    private function getValue(string $field)
    {
        if (array_key_exists($field, $this->getMail()->getAnswersByFieldMarker())) {
            if ($this->getMail()->getAnswersByFieldMarker()[$field]) {
                return $this->getMail()->getAnswersByFieldMarker()[$field]->getValue();
            }
        }

        return '';
    }
}
