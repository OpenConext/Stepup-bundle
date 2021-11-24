# 4.1.7
* Deprecate and remove U2F support #62

# 4.1.2 .. 4.1.6
No formal changelog entries for these versions. See PR's 57 through 61 for an overview of the changes.

# 4.1.1
Add accessor to Loa for the level field

# 4.1.0
Update version restraints to support Symfony 4.3 #56

# 4.0.4
This is a security release that will harden the application using this bundle against CVE 2019-3465
 * Upgrade Stepup-saml-bundle to version 4.1.8 #57
 * Get the Travis code quality checks to pass again #58

# 4.0.3
Update Symfony From version restraints

# 4.0.2
Provide a test double for the SmsSecondFactorService

# 4.0.1
Remove the choice list feature from the CountryCodeListing value object as this interferes with the Symfony 3.4 way
of working with choice types.

# 4.0.0
Added Symfony 3.4 support. This release might not work with StepUp components that do not use Symfony 3.4. This is due
to the change made to the FormFactory and the usage of it by the LocaleExtension.

# 3.5.2
Removed the lockfile from the project to prevent compatibility issues in newer Symfony versions.

# 3.5.1
Included in this release:

 * Fix deprecation warning about private service in symfony 3

# 3.5.0
Included in this release:

 * Experimental support for symfony 3
 * Make all configuration optional

# 3.4.2
Register the registration expiration helper as a service

# 3.4.1
Add helper for determining registration expiration date

# 3.4.0
Remove support for graylog.

# 3.3.3
Read request id from DI container when rendering error reports

# 3.3.2
Minor additions to error reporting improvements

# 3.3.1
Minor additions to error reporting improvements

# 3.3.0
In this release error reporting is improved.

## Improvements
 * Implement error page redesign #47
 * Improve art-code algorithm #48

# Older versions

## VERSION 3  RELEASE 3.0

   Version 3.2 - Addition of two reusable services
      14/03/2018 14:10  3.2.0  initial release

   Version 3.1 - Removal of SURFisms
      14/03/2018 09:00  3.1.1  Removal of isTiqr and isBiometric methods
      12/03/2018 15:33  3.1.0  initial release

   Version 3.0 - Removal of deprecated isGssf method
      30/11/2017 08:47  3.0.0  initial release

## VERSION 2  DYNAMIC CONFIGURATION OF SECOND FACTOR TYPES

   Version 2.0 - Dynamic configuration of second factor types
      14/06/2017 15:03  2.0.1  The SecondFactorTypeService is now public
      08/06/2017 13:22  2.0.0  initial release

## VERSION 1  RELEASE 1.0

   Version 1.7 - Updated Guzzle to Guzzle 6
      07/03/2017 14:44  1.7.0  initial release

   Version 1.6 - Added method to expose available second factor types
      08/02/2017 17:36  1.6.0  initial release

   Version 1.5 - Improved requestid handling
      03/08/2016 09:45  1.5.0  initial release

   Version 1.4 - Add ability for stepup project to set locale cookie
      08/06/2016 13:02  1.4.0  initial release

   Version 1.3 - Add biometric second factor type
      28/04/2016 14:40  1.3.0  initial release

   Version 1.2 - Allow Stepup Request ID to be atached to Guzzle client requests
      29/07/2015 17:18  1.2.0  initial release

   Version 1.1 - Restrictive Caching Headers
      14/07/2015 13:33  1.1.0  initial release

   Version 1.0 - Release 1.0
      19/06/2015 13:44  1.0.0  initial release

## VERSION 0  FIRST PILOT RELEASE

   Version 0.2 - Cryptographically secure OTP generation
      02/04/2015 16:06  0.2.0  initial release

   Version 0.1 - First pilot release
      26/03/2015 13:46  0.1.0  initial release
