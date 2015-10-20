# SAML IdP Provider module

## Installation
A few additional steps are required to get this module working.

1. It is recommended to install with composer, to manage dependencies.
1. Run the post-Install script, Drupal\saml_idp\Installer::postInstall(), by
  adding it to your project's composer.json file for auto-execution.
  If you don't do so, you will need to manually install and enable the "drupalauth"
  module in simplesamlphp.
1. Configure simplesamlphp's `authsources.php` to use drupalauth:External as an
  authentication service. A sample is contained in the `config-dist` directory.
