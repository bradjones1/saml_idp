<?php

namespace Drupal\saml_idp;

class Resume {
  public function resume() {
    // Drupal attempts to instantiate the called controller class,
    // so this basically acts as a wrapper around the static function.
    \sspmod_drupalauth_Auth_Source_External::resume();
  }
}
