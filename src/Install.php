<?php

namespace Drupal\saml_idp;

class Install {
  public static function postInstall() {
    $reflector = new \ReflectionClass('SimpleSAML_Configuration');
    $location = dirname(dirname(dirname($reflector->getFileName()))) . '/modules/drupalauth';
    $file = $location . '/default-enable';
    if (!file_exists($file)) {
      mkdir($location, 0775);
      touch($file);
      // @todo - Throw an error here if we're unsuccessful?
    }
  }
}
