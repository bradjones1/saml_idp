# SAML IdP Provider module

## Installation
A few additional steps are required to get this module working. This is an API
module and as such does not, out of the box, provide any working functionality.

1. It is recommended to install with composer, to manage dependencies.
1. Run the post-Install script, Drupal\saml_idp\Installer::postInstall(), by
  adding it to your project's composer.json file for auto-execution.
  If you don't do so, you will need to manually install and enable the "drupalauth"
  module in simplesamlphp.
1. Configure simplesamlphp's `authsources.php` to use drupalauth:External as an
  authentication service. A sample is contained in the `config-dist` directory.

### Developer notes
- The autoloader configuration in composer.json is required because, while
  simplesamlphp may be running in the same global project as Drupal, the latter
  may not be bootstrapped. Drupal adds PSR-4 paths by its pattern during bootstrap.
  Additionally, the classes provided for the SSP module are not namespaced and
  as such need special treatment.
- If you do not have a `sites/sites.php` Drupal will assume you are using the
  site defined at `sites/default`. If you are in a multisite environment, you
  may need to adjust your sites configuration to allow the SSP module to find
  the correct site configuration to use.
