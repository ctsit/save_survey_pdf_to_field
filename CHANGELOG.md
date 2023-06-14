# Change Log
All notable changes to the Save Survey PDF to Field project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).


## [1.1.3] - 2023-06-14
### Changed
- Mark the project as end-of-life (Philip Chase)
- Add contents of utility.php to ExternalModule namespace to appease PSALM (Kyle Chesney)
- Update authors to maintenance listserv address (Kyle Chesney)
- prevent array_keys(foo, NULL) from ocurring (Kyle Chesney)
- use foreach over indices as suggested by @jrpence (Kyle Chesney)
- remove debug comments, standardize whitespace, keep "index" (Kyle Chesney)

### Added
- Add example XML (Kyle Chesney)
- Cleaned code for submission and commented out debug logging (remi)
- Removed overload on `$count` in nested loop (remi)
- Imploded indices array for log (remi)
- Add debug logging (remi)
- First pass of correcting for multiple destinations (remi)


## [1.1.2] - 2018-08-22
### Changed
- Refactoring, preventing SQL injection. (Tiago Bember Simeao)
- Fixing unexpected behavior on longitudinal projects. (Tiago Bember Simeao)


## [1.1.1] - 2018-05-11
### Added
- Modify email url to use correct top-level directory and https (Dileep)


## [1.1.0] - 2018-05-04
### Added
- Log successes and failures of uploads and emails in the REDCap Event Log


## [1.0.0] - 2017-12-11
### Added
- Initial release of a tool to save a completed survey as PDF on a file upload field to keep a precise record of a survey.
