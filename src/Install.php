<?php

namespace Drupal\saml_idp;

use Composer\Script\Event;

class Install {
  public static function postInstall(Event $event) {
    $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
    require $vendorDir . '/autoload.php';
    $reflector = new \ReflectionClass(\SimpleSAML_Configuration::class);
    $location = dirname(dirname(dirname($reflector->getFileName()))) . '/modules/drupalauth';
    $file = $location . '/default-enable';
    if (!file_exists($file)) {
      mkdir($location, 0775);
      touch($file);
      // @todo - Throw an error here if we're unsuccessful?
    }
  }
}
