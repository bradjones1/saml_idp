# SAML IdP Provider module

Provides integration between Drupal 8 and [SimpleSAMLphp](https://simplesamlphp.org/)
to provide an Identity Provider (IdP) for SAML service providers (SPs).

## Installation
A few additional steps are required to get this module working. This is an API
module and as such does not, out of the box, provide any working functionality.

1. It is recommended to install with composer, to manage dependencies including
  simplesamlphp itself. See immediately below. Try [docker-drupal](https://github.com/BradJonesLLC/docker-drupal)
1. Run the post-Install script, `Drupal\saml_idp\Install::postInstall`, by
  adding it to your project's `composer.json` file under `post-install-cmd`.
  If you don't do so, you will need to manually enable the "drupalauth"
  module in simplesamlphp by creating an empty text file at
  `vendor/simplesamlphp/simplesamlphp/modules/drupalauth/default-enable`.
  Do not be mislead by the "drupalauth" project on Drupal.org, which is for
  Drupal 7. SSP only needs to be aware of the presence of the SSP auth module,
  which is autoloaded from the Drupal module.
1. Configure simplesamlphp's `authsources.php` to use `drupalauth:External` as an
  authentication service. A sample is contained in the `config-dist` directory.
1. Configure a `cookie_domain` value in your `services.yml` file (and any local
  versions you use for development.) Absent this value, Drupal will use a combination
  of hostname and base path to create cookie names, which is not helpful when
  Drupal is being run inside another application. Drupal provides a commented-out
  default with good documentation.
1. SimpleSAMLphp itself requires extensive configuration, including private key
  generation and the specification of service providers. See [the IdP QuickStart](https://simplesamlphp.org/docs/stable/simplesamlphp-idp)
  documentation for more information.

### Developer notes
- The autoloader configuration in `composer.json` is required because, while
  simplesamlphp may be running in the same global project as Drupal, the latter
  is not bootstrapped from the start. Drupal adds PSR-4 paths by its pattern.
  Additionally, the classes provided for the SSP module are not namespaced and
  as such need special treatment.
- If you are in a multisite environment (why? why?!), you may need to adjust your
  sites configuration to allow Drupal to find the correct site configuration to
  use. If you do not have a `sites/sites.php` file (as is typical) Drupal will assume
  you are using the site defined at `sites/default`.

## Copyright and License
&copy; 2015-2018 by Brad Jones LLC. Licensed under GPL 2.

Adapted from the [drupalauth module](https://code.google.com/p/drupalauth/) developed
by Steve Moitozo. Little if any of the original code remains due to the transition
to composer and Drupal 8, but many thanks for the outline!
