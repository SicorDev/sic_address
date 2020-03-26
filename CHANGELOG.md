# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.2] - 2020-03-26

### Fixed
- brought back import of tt_address fields
  > was accidentally removed in 2.4.0

## [2.4.1] - 2020-03-13

### Changed
- successfully tested with PHP 7.3 and PHP 7.4
  > ATTENTION: TYPO3 8.7.28 or 9.5.10 required !!!

  > those both contain the required Fluid Engine 2.6.4 update
- image link logic added to image partial
- removed an obsolete variable

### Fixed
- fixed parameter "absolute" in an uri.action

## [2.4.0] - 2020-03-05

### Added
- CSV import module
- check if sic_address was updated and domain properties already saved
  > in this case a flash error is shown both in backend and frontend to inform the developer to run the generate process again

### Changed
- added new css style to IrseeList template
- did some code styling
- removed some obsolete code

### Fixed
- fixed some translations
- fixed some typos [#36](https://github.com/SicorDev/sic_address/pull/36)

## [2.3.2] - 2020-01-21

### Added
- dropdown for ascending and descending sorting order added to plugin

### Changed
- in the plugin, you can now choose integer fields as sorting field as well, too.
- internal/external select changed to radio buttons

### Fixed
- brought back slider partial for UnigesList template

## [2.3.1] - 2020-01-10

### Fixed

- new auto template was missing condition and had a wrong partial path

## [2.3.0] - 2020-01-09

### Added
- auto generation of fluid partial for address; based on given properties
  > this partial contains all your configured properties. You can easily copy it to fileadmin and customize it there.

  > it's NOT meant to be used on-the-fly in production mode !

- added "auto generated" comment to all generated files, including datetime of last generation and a warning.

- added logic for activation of default ordering for address records in backend
  > default ordering was used in frontend already, if no ordering field was choosen in plugin

- added "clickenlarge" logic to plugin, too.

- added logic to show multiple address records in detail view

- [Microdata](https://www.w3.org/TR/microdata/)/semantics added to Show/Multiple template.
  > itemscope and itemprop

### Changed
- moved inline condition for property panel class to own section
- did some code styling
- translated "Kategorien" in Show/Multiple template

### Fixed
- merged pull requests from [Franz Holzinger](https://github.com/franzholz)
  > tt_address telephone is phone [#31](https://github.com/SicorDev/sic_address/pull/31)

  > remove leading and trailing blanks [#32](https://github.com/SicorDev/sic_address/pull/32)

  > misspelling contentReposirory [#34](https://github.com/SicorDev/sic_address/pull/34)

- fixed translation for "ignore demands" option in plugin

## [2.2.1] - 2019-11-26

### Fixed
- added CHARSET option to vCard string properties
  > this fixes vCard import to several mail clients like Outlook etc. 

## [2.2.0] - 2019-11-04

### Added
- [vCard](https://de.wikipedia.org/wiki/VCard). Link integrated in sicor list view template.
  > sponsored by [Quadronet](https://quadronet.de/)

## [2.1.3] - 2019-10-15

### Fixed
- removed restriction of tt_address fields, got from TCA columns
  > some important fields had been ignored (title, description, ...)
  
### Added
- placeholder added to property fields
  > **TCA Override (Array)**
  
  > **Settings (Array)** 

## [2.1.2] - 2019-09-30

### Fixed
- respect language in address search [#26](https://github.com/SicorDev/sic_address/issues/26)
  > misseed after pull [#23](https://github.com/SicorDev/sic_address/pull/23)
- optimized error handling in language files generation

## [2.1.1] - 2019-09-06

### Fixed
- set english label for title in ext_tables.php:addStaticFile(..., title)
  > no localization logic available here

## [2.1.0] - 2019-09-02

### Added
- Respect available language records in AtoZ menue [#23](https://github.com/SicorDev/sic_address/pull/23)
- "ignore demands" option in plugin
  > in case you want to use multiple sic_address plugins on the same page and do not want all of them to react to search parameters
- pass data array of plugin to fluid templates
  > now you can use content element fields (header,subheader,...) in your customized sic_address fluid templates  
- german language files
- handling locales for tcaLabel
  > you can translate your tca labels into all installed languages
- generate locallang_db.xlf for every installed language
  > added fluid logic in generation template to separate between source and target language

### Changed
- localized class and template files
- localized Export and Doublet module, too
- labels in default language file(s) changed to english
- localized extension configuration file, too
- enable developer module in extension configuration
  > very useful on first install
- get tt_address fields from TCA instead of DB 

### Fixed
- removed view helper "f:form.select.option" from select box used for "internal/external" properties
  > available in TYPO3 v8 and above (throws error in TYPO3 v7)
- ignore external fields if tt_address mapping disabled
  > do not use imported tt_address fields anymore if/after mapping of tt_address gets disabled
 

### Removed
- obsolete code
  > use statements for unused classes

  > unused controllers, methods and templates
