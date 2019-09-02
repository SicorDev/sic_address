# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
  > added fluid logic generation template to separate between source and target language
- localized Export and Doublet module, too

### Changed
- localized class and template files
- labels in default language file(s) changed to english
- localized extension configuration file, too
- enable developer module in extension configuration
  > very useful on first install
- get tt_address fields from TCA instead of DB 

### Fixed
- removed view helper "f:form.select.option" for select box used for "internal/external" properties
  > available in TYPO3 v8 and above (throws error in TYPO3 v7)
- ignore external fields iff tt_address mapping off
  > do not use tt_address field anymore after mapping gets disabled
 

### Removed
- obsolete code
  > old use statements for unused classes

  > old controllers, methods and templates
