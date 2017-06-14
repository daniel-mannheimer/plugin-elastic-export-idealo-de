# Release Notes for Elastic Export idealo.de

## v1.0.14 (2017-06-14)

### Changed
- Smaller adjustments on the format of user guide were made.

## v1.0.13 (2017-06-13)

### Changed
- The description of the plugin was enhanced.
- Properties of the type "Decimal number" or "whole number" can now be used for this format. 
- The tag to enable a variation for Idealo Direktkauf in the column "checkoutApproved" will now also be set true if the referrer "idealo Direktkauf" was set available for this variation.

## v1.0.12 (2017-05-12)

### Fixed
- An issue was fixed which caused the variations not to be exported in the correct order.
- An issue was fixed which caused the export format to export texts in the wrong language.

## v1.0.11 (2017-05-05)

### Fixed
- An issue was fixed which caused errors while loading the export format.

## v1.0.10 (2017-05-02)

### Changed
- Outsourced the stock filter logic to the Elastic Export plugin.

## v1.0.9 (2017-04-27)

### Fixed
- Stock is now correctly evaluated.

## v1.0.8 (2017-04-18)

### Fixed
- Logs are now correctly translated.
- The array definitions of the result fields are now correctly defined for the KeyMutator.
- Stock is now correctly calculated.
- Payment Methods are now correctly evaluated.

## v1.0.7 (2017-04-12)

### Fixed
- Try-catch to catch errors now works as intended.
- The format plugin is now only based on Elastic Search.
- The performance has been improved.
- The values ​​for the column fulfillmentType are now correctly evaluated.

## v1.0.6 (2017-03-30)

### Added
- Added a new mutator so we will prevent trying to get access to an array key which not exists.

## v1.0.5 (2017-03-28)

### Changed
- The process was changed at some positions to improve the stability.

## v1.0.4 (2017-03-22)

### Added
- Logs

### Changed
- The process was changed at some positions to increase the performance.

## v1.0.3 (2017-03-22)

### Fixed
- We now use a different value to get the image URLs for plugins working with elastic search.

## v1.0.2 (2017-03-13)

### Added
- Added marketplace name.

### Changed
- Updated plugin icons.

## v1.0.1 (2017-03-03)

# Changed
- From now on a SKU will be generated for each exported variation.
- Adjustment for the ResultField, so the imageMutator does not affect the image outcome anymore if the referrer "ALL" is set.

## v1.0.0 (2017-02-20)
 
### Added
- Added initial plugin files
