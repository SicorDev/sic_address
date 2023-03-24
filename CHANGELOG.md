# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.1.0] - 2023-03-24

### Added
- New Template duelmenList

### Changed
- Speedup for String Filters

### Fixed
- Pagination should keep parameters now

## [4.0.0] - 2022-12-29

### Major
- This the first official release for TYPO3 11 and PHP 7.4 or higher.
- Compatibility to 9/10 dropped due to massive core changes. 

### Changed
- Fully integrated Map into List Action. Please reconfigure your frontend Plugins after upgrading.

### Added
- Count adresses per category!
- Option for route planner link

## [3.2.0] - 2022-02-17

### Added
- Enhanced filter to allow string fields
  > Sponsored by Städtisches Klinikum Görlitz gGmbH 

### Changed
- Updated all Map related code and templates with our first map project finally going live soon.  
- Removed all remaining google maps related code.

### Warning
- Not compatible with tt_address 6.* at the moment!

## [3.1.5] - 2021-11-30

### Changed
- Removed unused eundwList template.
- Updated massivList template, still WIP though.
- Improved Map fit-bounds by using circle bounds.

## [3.1.4] - 2021-11-24

### Fixed
- Made A-Z Filter respect current language again. (Issue 45)
- Improved TCA generation even more.

## [3.1.3] - 2021-11-24

### Fixed
- Prevent creation of duplicate DB entries.
- Improved TCA generation.

### Changed
- Added composer.json submitted via pull request.

## [3.1.2] - 2020-12-09

- Stupidity Re-Upload...

## [3.1.1] - 2020-12-09

### Fixed
- Uplodaded correct yaml file
- Fixed pagination in list templates

### Changed
- Had to drop compatibility to 8.7 due to class mapping problems

## [3.1.0] - 2020-08-14

### Added
- A-Z partial and demo yaml file for URL routing.

### Changed
- Returned to stable.
- Prepared 3 templates for URL routing. 

## [3.0.2] - 2020-08-14

### Fixed
- Usage without tt_address.

## [3.0.1] - 2020-08-14

### Changed
- Field and direction for Sorting now visible in plugin's map view

## [3.0.0] - 2020-08-13

### Changed
- First version for TYPO3 10, dropped support for TYPO3 7
- Dedicated to the memory of Adnan Zelkanovic (*1977 - †2020)

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
