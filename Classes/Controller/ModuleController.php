<?php

namespace SICOR\SicAddress\Controller;

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

use Psr\Http\Message\ResponseInterface;

use SICOR\SicAddress\Domain\Model\Address;
use SICOR\SicAddress\Domain\Model\DomainObject\BooleanType;
use SICOR\SicAddress\Domain\Model\DomainObject\ChecklistType;
use SICOR\SicAddress\Domain\Model\DomainObject\FloatType;
use SICOR\SicAddress\Domain\Model\DomainObject\ImageType;
use SICOR\SicAddress\Domain\Model\DomainObject\IntegerType;
use SICOR\SicAddress\Domain\Model\DomainObject\LinkType;
use SICOR\SicAddress\Domain\Model\DomainObject\RichType;
use SICOR\SicAddress\Domain\Model\DomainObject\SelectType;
use SICOR\SicAddress\Domain\Model\DomainObject\StringType;
use SICOR\SicAddress\Domain\Model\DomainProperty;
use SICOR\SicAddress\Domain\Repository\AddressRepository;
use SICOR\SicAddress\Domain\Repository\CategoryRepository;
use SICOR\SicAddress\Domain\Repository\DomainPropertyRepository;
use SICOR\SicAddress\Domain\Service\ConfigurationService;
use StdClass;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\View\NotFoundView;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use function str_replace;

/**
 * ModuleController
 */
class ModuleController extends AbstractController
{
    protected ?AddressRepository $addressRepository;
    protected ?CategoryRepository $categoryRepository;
    protected ?DomainPropertyRepository $domainPropertyRepository;
    protected ?ModuleTemplate $moduleTemplate = null;

    /**
     * Holds all domainProperties
     *
     * @var array
     */
    protected array $configuration = [];

    /**
     * Fixed set of fieldTypes
     *
     * @var array
     */
    protected $fieldTypes = array("string", "integer", "select", "image", "rich", "boolean", "float", "checklist", "mmtable", "link");

    /**
     * Holds the Typoscript configuration
     *
     * @var Configuration
     */
    protected $extbaseFrameworkConfiguration = NULL;

    /**
     * Holds the Extension configuration
     *
     * @var array
     */
    protected $extensionConfiguration = NULL;

    /**
     * Template Root Path
     *
     * @var string
     */
    protected $templateRootPath = "";

    /**
     * @var int
     */
    protected $external = 0;

    public function __construct(
        AddressRepository $addressRepository,
        CategoryRepository $categoryRepository,
        DomainPropertyRepository $domainPropertyRepository,
        protected readonly PageRenderer $pageRenderer,
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly InstallUtility $installUtility
    )
    {
        $this->addressRepository = $addressRepository;
        $this->categoryRepository = $categoryRepository;
        $this->domainPropertyRepository = $domainPropertyRepository;
    }

    public function initializeAction(): void
    {
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->extensionConfiguration = ConfigurationService::getConfiguration();
        if(!empty($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'])) {
            $this->templateRootPath = GeneralUtility::getFileAbsFileName($this->extbaseFrameworkConfiguration['view']['codeTemplateRootPaths'][0]);
        }

        if (!empty($this->extensionConfiguration['ttAddressMapping'])) {
            if (empty($GLOBALS['TCA']['tt_address'])) {
                $this->extensionConfiguration['ttAddressMapping'] = null;
            }
        }

        $this->external = $this->request->hasArgument('external') ? abs($this->request->getArgument('external')) : 0;
        if ($this->request->getControllerActionName() === 'list' || empty($this->extensionConfiguration['ttAddressMapping'])) {
            $this->configuration = $this->domainPropertyRepository->findByExternal($this->external);
        } else {
            $this->configuration = $this->domainPropertyRepository->findAll();
        }

        foreach ($this->configuration as $key => $languages) {
            foreach ($languages as $language => $value) {
                $type = $this->configuration[$key][$language]->getType();
                if(is_string($type)) {
                    // Initialize Type Objects
                    $class = GeneralUtility::makeInstance("SICOR\\SicAddress\\Domain\\Model\\DomainObject\\" . ucfirst($type) . "Type");
                    $this->configuration[$key][$language]->setType($class);
                }
            }
        }

        // Reset JavaScript and CSS files
        GeneralUtility::makeInstance(PageRenderer::class);
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        if ($this->extensionConfiguration['ttAddressMapping'] === null) {
            $this->addFlashMessage($this->translate('flash_tt_address_missing'), $this->translate('flash_warning'), ContextualFeedbackSeverity::WARNING, FALSE);
        }

        // Bailout if static template is missing
        if ($this->view instanceof NotFoundView) {
            if (!$this->extbaseFrameworkConfiguration['view'] || !array_key_exists('templateRootPaths', $this->extbaseFrameworkConfiguration['view'])) {
                echo "Static typoscript template hasn't been included in this branch.";
                exit;
            }
        }
        if ($this->request->hasArgument('errorMessages')) {
            $this->moduleTemplate->assign("errorMessages", $this->request->getArgument('errorMessages'));
        }
        $this->moduleTemplate->assign('ttAddressMapping', $this->extensionConfiguration['ttAddressMapping']);
        $this->moduleTemplate->assign("properties", $this->configuration);
        $this->moduleTemplate->assign("fieldTypes", $this->getFieldTypeList());
        $this->moduleTemplate->assign('external', $this->external);
        $types = array(0 => $this->translate('internal'));
        if ($this->extensionConfiguration['ttAddressMapping']) {
            $types[1] = $this->translate('external');
        }
        $this->moduleTemplate->assign('types', $types);
        $this->moduleTemplate->assign('languages', $this->request->getAttribute('site')->getLanguages());

        return $this->wrapModuleTemplate('Module/List');
    }

    /**
     * action save
     *
     * @return void
     */
    public function createAction(): ResponseInterface
    {
        $errorMessages = array();
        debug ('B');

        // Model
        $domainProperties = array();
        foreach ($this->configuration as $key => $value) {
            if (is_array($value)) $value = $value[0];

            if ($value->getHidden()) continue;
            if ($value->_getProperty('_languageUid')) continue;

            $title = GeneralUtility::underscoredToLowerCamelCase($value->getTitle());
            $value->getType()->setClassName($title);

            if ($value->getType()->getTitle() === "mmtable") {
                if (!$this->saveTemplate("Classes/Domain/Model/" . ucfirst($title) . ".php", $value, "Classes/Domain/Model/Table.php"))
                    $errorMessages[] = "Unable to save Model: " . ucfirst($title) . ".php";
                if (!$this->saveTemplate("Configuration/TCA/tx_sicaddress_domain_model_" . strtolower($title) . ".php", $this->getSingleTCAConfiguration($value), "Configuration/TCA/tx_sicaddress_domain_model_table.php"))
                    $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_" . strtolower($title) . ".php";
            }

            $domainProperties[$key] = clone $value;
            $domainProperties[$key]->setTitle($title);
        }
        debug ($domainProperties, 'createAction $domainProperties');

        if (!$this->saveTemplate('Classes/Domain/Model/Address.php', $domainProperties))
            $errorMessages[] = "Unable to save Model: Address.php";

        // SQL
        if (!$this->saveTemplate('ext_tables.sql', $this->getSQLConfiguration()))
            $errorMessages[] = "Unable to save SQL: ext_tables.sql";

        // TCA
        $extPath = ExtensionManagementUtility::extPath("sic_address");
        if ($this->extensionConfiguration["ttAddressMapping"]) {
            // tt_address override
            if (!$this->saveTemplate('Configuration/TCA/Overrides/tt_address.php', $this->getTCAConfiguration()))
                $errorMessages[] = "Unable to save Table Mapping Overrides: tt_address.php";
            $delFile = $extPath . 'Configuration/TCA/tx_sicaddress_domain_model_address.php';
            if (is_file($delFile)) unlink($delFile);
        } else {
            // tx_sicaddress_domain_model_address
            $tca = $this->getTCAConfiguration();

            // Create orderby and headline according to config
            $headline = "";
            $orderby = [];
            foreach ($tca as $value) {
                if ($value['isListLabel']) {
                    $orderby[] = $value['title'];
                    if (empty($headline))
                        $headline = $value['title'];
                }
            }
            $orderbyquery = empty($orderby) ? '' : "ORDER BY " . implode(',', $orderby);

            if (!$this->saveTemplate('Configuration/TCA/tx_sicaddress_domain_model_address.php', $this->getTCAConfiguration(), "", $headline, $orderbyquery))
                $errorMessages[] = "Unable to save TCA: tx_sicaddress_domain_model_address.php";
            $delFile = $extPath . 'Configuration/TCA/Overrides/tt_address.php';
            if (is_file($delFile)) unlink($delFile);
        }

        // Table Mapping
        if (!$this->saveTemplate('ext_typoscript_setup.typoscript', $this->extensionConfiguration))
            $errorMessages[] = "Unable to save Table Mapping: ext_typoscript_setup.typoscript";

        // Language
        $generationResults = $this->saveLanguageTemplates('Resources/Private/Language/###prefix###locallang_db.xlf', $this->configuration);
        if ($generationResults !== true)
            $errorMessages[] = $generationResults;

        // Show Template
        if (!$this->createShowTemplate($domainProperties, 'Resources/Private/Partials/Address/AutoGeneratedPropertyList.html'))
            $errorMessages[] = "Unable to create Show Template: PropertyList.html";

        $this->installUtility->install($this->request->getControllerExtensionKey());
        $this->moduleTemplate->assign("errorMessages", $errorMessages);

        if(empty($errorMessages)) {
            $delFile = $extPath.'PLEASE_GENERATE';
            if (is_file($delFile)) unlink($delFile);
        }
        debug ('E');

        return $this->wrapModuleTemplate('Module/Create');
    }

    /**
     * Create Show Template
     * @param $domainProperties
     * @param $targetFilename
     */
    public function createShowTemplate($domainProperties, $targetFilename)
    {
        $content = '<f:comment>
    Auto generated on '.date('c').'!
    Do NOT edit !!!

    If you need to customize it, make a copy in fileadmin, please.
</f:comment>

';
        $content .= '<div itemscope class="sicaddress-address" itemid="{f:uri.action(action:\'show\',absolute:true,arguments:\'{pageUid:settings.detailPagefield,address:address}\')}">' . "\n\n";
        foreach ($domainProperties as $prop) {

            if (!$prop->getType() instanceof BooleanType) {
                $content .= "\t" . '<f:if condition="{address.' . $prop->getTitle() . '}">' . "\n";
                $content .= "\t" . '<span itemprop="' . $prop->getTitle() . '"';
            }

            if ($prop->getType() instanceof StringType) {
                $content .= ' class="sicaddress-string sicaddress-' . $prop->getTitle() . '">' . "\n";
                $content .= "\t\t\t{address." . $prop->getTitle() . "}<br>\n";
            }
            if ($prop->getType() instanceof IntegerType) {
                $content .= ' class="sicaddress-integer sicaddress-' . $prop->getTitle() . '">' . "\n";
                $content .= "\t\t\t{address." . $prop->getTitle() . "}<br>\n";
            }
            if ($prop->getType() instanceof LinkType) {
                $content .= ' class="sicaddress-link sicaddress-' . $prop->getTitle() . '">' . "\n";
                $content .= "\t\t\t{address." . $prop->getTitle() . "}<br>\n";
            }
            if ($prop->getType() instanceof FloatType) {
                $content .= ' class="sicaddress-float sicaddress-' . $prop->getTitle() . '">' . "\n";
                $content .= "\t\t\t{address." . $prop->getTitle() . "}<br>\n";
            }
            if ($prop->getType() instanceof SelectType) {
                $content .= ' class="sicaddress-select sicaddress-' . $prop->getTitle() . '">' . "\n";
                $content .= "\t\t\t{address." . $prop->getTitle() . "}<br>\n";
                $content .= "\t\t" . '<f:switch expression="{address.'. $prop->getTitle() . '}">' . "\n";
                $selectString = explode("\n", $prop->getSettings());

                foreach ($selectString as $string) {
                    $content .= "\t\t\t" . '<f:case value="' . $string . '">' . "\n";
                    $content .= "\t\t\t" . "</f:case>\n";
                }
                $content .= "\t\t" . "</f:switch>\n";

            }
            if ($prop->getType() instanceof ImageType) {
                $content .= ' class="sicaddress-image sicaddress-' . $prop->getTitle() . '">' . "\n";
                $content .= "\t\t" . '<f:render partial="Address/Images.html" arguments="{_all}" />' . "\n";
            }
            if ($prop->getType() instanceof RichType) {
                $content .= ' class="sicaddress-RTE">' . "\n";
                $content .= "\t\t\t" . "<f:format.html parseFuncTSPath=\"lib.parseFunc_RTE\">";
                $content .= "{address." . $prop->getTitle() . "}";
                $content .= "</f:format.html>\n";

            }
            if ($prop->getType() instanceof BooleanType) {
                $content .= "\t" . '<f:if condition="{address.' . $prop->getTitle() . '}">' . "\n";
                $content .= "\t\t" . '<f:then>' . "\n";
                $content .= "\t\t" . '<span itemprop="' . $prop->getTitle() . '" data-value="true">' . "\n";
                $content .= "\t\t" . '</span>' . "\n";
                $content .= "\t\t" . '</f:then>' . "\n";
                $content .= "\t\t" . '<f:else>' . "\n";
                $content .= "\t\t" . '<span itemprop="' . $prop->getTitle() . '" data-value="false">' . "\n";
                $content .= "\t\t" . '</span>' . "\n";
                $content .= "\t\t" . '</f:else>' . "\n";
                $content .= "\t" . "</f:if>\n\n";
            }
            if ($prop->getType() instanceof ChecklistType) {
                $content .= ' class="sicaddress-checklist' . '">' . "\n";
                $selectString = explode("\n", $prop->getSettings());
                $i = 0;
                foreach ($selectString as $string) {
                    $i = $i + 1;
                    $content .= "\t\t\t" . '<f:if condition="{address.' . $prop->getTitle() . '} == \'' . $i . '\'">' . "\n";
                    $content .= "\t\t\t" . "</f:if>\n";
                }
            }

            if (!$prop->getType() instanceof BooleanType) {
                $content .= "\t" . '</span>' . "\n";
                $content .= "\t" . "</f:if>\n\n";
            }

        }
        $content .= "</div>\n";
        $filename = ExtensionManagementUtility::extPath("sic_address") . $targetFilename;
        return (boolean)file_put_contents($filename, $content);
    }


    /**
     * Delete DomainProperties
     * @param string $external
     */
    public function deleteFieldDefinitionsAction($external): ResponseInterface
    {
        $this->domainPropertyRepository->deleteFieldDefinitions($external);
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->persistAll();
        return new ForwardResponse('list');
    }

    /**
     * action importTTAddress
     * @return void
     */
    public function importTTAddressAction(): ResponseInterface
    {
        if ($this->extensionConfiguration["ttAddressMapping"]) {
            // Clear database
            $this->domainPropertyRepository->deleteFieldDefinitions(1);

            $fields = $this->addressRepository->getFields();
            foreach ($fields as $field) {
                if (preg_match("/^(uid|pid|cruser_id|t3.*|tstamp|deleted|categories|sorting|hidden)$/", $field) === 0) {
                    $domainProperty = new DomainProperty();
                    $domainProperty->setTitle($field);
                    $domainProperty->setTcaLabel($field);
                    switch ($field) {
                        case 'image':
                            $domainProperty->setType('image');
                            break;
                        default:
                            $domainProperty->setType('string');
                    }
                    $domainProperty->setExternal(!str_starts_with($field, 'tx_'));
                    $this->domainPropertyRepository->add($domainProperty);
                }
            }
        }
        return new ForwardResponse('list');
    }

    /**
     * Get SQL Configuration from Model
     *
     * @return array
     */
    private function getSQLConfiguration()
    {
        $sql = array();
        foreach ($this->configuration as $key => $value) {
            if (is_array($value)) $value = $value[0];

            if (!$value->getHidden() && !$value->isExternal()) {
                $sql[$key]["definition"] = $value->getType()->getSQLDefinition('`' . $value->getTitle() . '`');
                $sql[$key]["title"] = $value->getTitle();
                $sql[$key]["type"] = $value->getType($value->getTitle());
            }
        }
        return $sql;
    }

    /**
     * Get TCA Configuration from Model
     *
     * @return array
     */
    private function getTCAConfiguration()
    {
        $tca = array();
        foreach ($this->configuration as $key => $value) {
            if (is_array($value)) $value = $value[0];

            if (!$value->getHidden()) $tca[] = $this->getSingleTCAConfiguration($value);
        }
        return $tca;
    }

    /**
     * Get single TCA Configuration from Model
     *
     * @param $value
     * @return array
     */
    private function getSingleTCAConfiguration($value)
    {
        // Build options for dropdowns or checklists
        $options = "";
        $settings = array();
        if ($value->getType()->getTitle() === "select" || $value->getType()->getTitle() === "checklist") {
            // Dropdown options
            if (!empty($value->getSettings())) {
                $dropdownOptions = explode("\n", $value->getSettings());
                for ($i = 0; $i < count($dropdownOptions); $i++) {
                    $options .= 'array("' . trim($dropdownOptions[$i]) . '", ' . ($i + 1) . '),';
                }
            }
        } else {
            // Settings
            parse_str($value->getSettings(), $settings);
        }

        // Render the TCA snippets
        $templatePathAndFilename = $this->templateRootPath . "Resources/Private/Partials/" . ucfirst($value->getType()->getTitle()) . "Type.tca";
        $customView = GeneralUtility::makeInstance(StandaloneView::class);
        $customView->setTemplatePathAndFilename($templatePathAndFilename);

        $customView->assign("properties", $settings);
        $customView->assign("options", $options);
        $customView->assign("title", $value->getTitle());
        $config = $customView->render();

        // Eventually override TCA
        if ($value->getTcaOverride()) {
            $config = $value->getTcaOverride();
        }

        $tca = array("external" => $value->isExternal(), "title" => $value->getTitle(), "tcaLabel" => $value->getTcaLabel(), "type" => $value->getType(), "isListLabel" => $value->getIsListLabel(), "config" => $config);
        return $tca;
    }


    /**
     * Save Templates
     *
     * @param $filename
     * @param $properties
     * @param $templatePath
     *
     * @return bool
     */
    private function saveTemplate($filename, $properties, $templatePath = "", $headline = "", $orderbyquery = "", $prefix = '')
    {
        $customView = GeneralUtility::makeInstance(StandaloneView::class);

        $targetFilename = str_replace('###prefix###', $prefix ? $prefix . '.' : '', $filename);
        $filename = str_replace('###prefix###', '', $filename);

        $templatePathAndFilename = !$templatePath ? $this->templateRootPath . $filename : $this->templateRootPath . $templatePath;
        debug ($templatePathAndFilename, 'saveTemplate $templatePathAndFilename');
        $customView->setTemplatePathAndFilename($templatePathAndFilename);
        $customView->setPartialRootPaths([$templatePathAndFilename]);
        $customView->assign("settings", $this->extensionConfiguration);
        $customView->assign("properties", $properties);
        $customView->assign("headline", $headline);
        $customView->assign("orderbyquery", $orderbyquery);
        $customView->assign('prefix', $prefix);
        $customView->assign('now', $ts=date('c'));

        $content = $customView->render();
        debug ($targetFilename, 'saveTemplate $targetFilename');
        debug ($content, 'saveTemplate $content');
        $content = str_replace('{now}', $ts, $content);
        $filename = ExtensionManagementUtility::extPath("sic_address") . $targetFilename;

        return (boolean)file_put_contents($filename, $content);
    }


    /**
     * Save language templates
     *
     * @param string $filename
     * @param array $properties
     * @return boolean|string
     */
    private function saveLanguageTemplates($filename, $properties)
    {
        $locales = array();

        foreach ($properties as $key => $l) {
            foreach ($l as $languageUid => $property) {
                $locales[$languageUid][$key] = $property;
            }
        }

        $languages = $this->request->getAttribute('site')->getLanguages();
        foreach ($locales as $languageUid => $localeProperties) {
            $prefix = $languageUid ? $languages[$languageUid]['iso'] : '';
            if (!$this->saveTemplate($filename, $localeProperties, '', '', '', $prefix))
                return 'Unable to save Locallang: ' . $prefix . ($prefix ? '.' : '') . 'locallang_db.xlf';
        }

        return true;
    }

    /**
     * Generate FieldTypeList for Select ViewHelper
     *
     * @return array
     */
    private function getFieldTypeList()
    {
        $result = array();
        foreach ($this->fieldTypes as $fieldType) {
            $field = new StdClass();
            $field->key = $fieldType;
            $field->value = ucfirst($fieldType);

            $result[] = $field;
        }
        return $result;
    }

    /**
     * Show doublet finder page for address entries
     *
     * @return void
     */
    public function doubletsAction(): ResponseInterface
    {
        $args = $this->request->getArguments();
        $properties = $this->getRelevantOnly($this->domainPropertyRepository->findAll());
        $letters = $fields = [];
        $sources = [
            'internal' => 1,
            'external' => 0
        ];
        if (!empty($args['sources'])) {
            $sources = $args['sources'];
        }
        if (!empty($args['fields'])) {
            $fields = $args['fields'];
        }

        foreach ($properties as $property) {
            if (is_array($property)) $property = $property[0];

            $source = $property->getExternal() ? 'external' : 'internal';
            $title = trim($property->getTitle());
            $tcaLabel = trim($property->getTcaLabel());
            if ($title !== $tcaLabel) $tcaLabel .= ' (' . $title . ')';
            $letter = strtoupper($title[0]);
            if ($sources[$source]) {
                $letters[$letter][$source][$title] = [
                    'title' => $title,
                    'label' => $tcaLabel,
                    'checked' => !empty($fields[$title])
                ];
                ksort($letters[$letter][$source]);
                krsort($letters[$letter]);
            }
        }
        ksort($letters);
        $this->moduleTemplate->assign('letters', $letters);
        $this->moduleTemplate->assign('sources', $sources);
        $this->moduleTemplate->assign('fields', $fields);

        if (!empty($fields)) {
            $pages = $this->addressRepository->findDoublets($fields);
            $this->moduleTemplate->assign('pages', $pages);
        }

        return $this->wrapModuleTemplate('Module/Doublets');
    }

    /**
     * This action shows the value of selected dataset doublets
     *
     * @return void
     */
    public function ajaxDoubletsAction(): ResponseInterface
    {
        $properties = $this->domainPropertyRepository->findAll();
        $args = $this->request->getArguments();
        $addresses = $this->addressRepository->findByArgs($args);
        $this->view->assign('addresses', $addresses);
        $this->view->assign('properties', $this->getRelevantOnly($properties));

        return $this->htmlResponse();
    }

    /**
     * Get onnly relevant properties from given property list.
     *
     * @param array $properties
     * @return void
     */
    protected function getRelevantOnly($properties)
    {
        $relevantProperties = array();

        foreach ($properties as $property) {
            if (is_array($property)) $property = $property[0];

            if ($this->addressRepository->isRelevant($property->getTitle())) {
                $relevantProperties[] = $property;

            }
        }

        return $relevantProperties;
    }

    /**
     * Remove action for address entries called over ajax
     *
     * @param Address $address
     * @return void
     */
    public function ajaxDeleteDoubletAction($address): ResponseInterface
    {
        $this->addressRepository->remove($address);
        return $this->jsonResponse(json_encode(array()));
    }

    /**
     * Switch value of given property for given source and target object
     *
     * @return void
     */
    public function switchDatasetsAction(): ResponseInterface
    {
        $sourceUid = $this->request->hasArgument('source') ? $this->request->getArgument('source') : 0;
        $targetUid = $this->request->hasArgument('target') ? $this->request->getArgument('target') : 0;
        $property = $this->request->hasArgument('property') ? $this->request->getArgument('property') : 0;
        $property = GeneralUtility::underscoredToUpperCamelCase($property);
        $propertyGetter = 'get' . $property;
        $propertySetter = 'set' . $property;
        $sourceDataset = $this->addressRepository->findByUid($sourceUid);
        $targetDataset = $this->addressRepository->findByUid($targetUid);

        if ($sourceDataset && method_exists($sourceDataset, $propertyGetter)) {
            $value = $sourceDataset->$propertyGetter();

            if ($targetDataset && method_exists($targetDataset, $propertySetter)) {
                $targetDataset->$propertySetter($value);
                $this->addressRepository->update($targetDataset);
            }
        }

        return $this->htmlResponse();;
    }

    protected function wrapModuleTemplate(string $template): Response
    {
        // Inject js and css
        $this->pageRenderer->loadJavaScriptModule('@sicor/sic-address/module_sicaddress.js');
        $this->pageRenderer->addCssFile('EXT:sic_address/Resources/Public/CSS/module_sicaddress.css');
        return $this->moduleTemplate->renderResponse($template);
    }
}
