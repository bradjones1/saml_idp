<?php

namespace Drupal\saml_idp;

class Install {
  public static function postInstall() {
    $reflector = new \ReflectionClass('SimpleSAML_Configuration');
    $location = dirname(dirname($reflector->getFileName()) . '/modules/drupalauth');
    mkdir($location, 0555);
    touch($location . '/default-enable');
    // @todo - Throw an error here if we're unsuccessful?
  }
}
