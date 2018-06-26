<?php

/**
 * @file
 * Hook definitions for the saml_idp module.
 */

use Drupal\user\UserInterface;

/**
 * @addtogroup hooks
 * @{
 */

 /**
  * Alter user attributes passed in SAML responses.
  *
  * @param array $attributes
  *   Associative array of attributes to be passed in the SAML response.
  * @param UserInterface $user_entity
  *   The user entity for the authenticated account
  */
 function hook_saml_idp_attributes_alter(&$attributes, UserInterface &$user_entity) {}

 /**
  * Perform an action when a user successfully authenticates through SAML.
  *
  * @param array $state
  *   The state after the login has completed.
  */
 function hook_saml_idp_login_completed($state) {}

 /**
  * Perform an action when a user successfully reauthenticates through SAML.
  *
  * @param array $state
  *   The state after the reauthentication has completed.
  */
 function hook_saml_idp_reauthenticated($state) {}

/**
 * @} End of "addtogroup hooks".
 */
