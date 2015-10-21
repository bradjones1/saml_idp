<?php


/**
 * Drupal authentication source configuration parser.
 *
 * Originally by Steve Moitozo, <steve_moitozo@sil.org>, http://www.sil.org
 * Modified by: Brad Jones, <brad@bradjonesllc.com>, http://bradjonesllc.com
 *
 * This class is a Drupal authentication source which authenticates users
 * against a Drupal site located on the same server.
 *
 */
class sspmod_drupalauth_ConfigHelper {

  /**
  * String with the location of this configuration.
  * Used for error reporting.
  */
  private $location;

  /**
  * Whether debug output is enabled.
  *
  * @var bool
  */
  private $debug;

  /**
  * The attributes we should fetch. Can be NULL in which case we will fetch all attributes.
  */
  private $attributes;

  /**
  * The name of the cookie
  */
  private $cookie_name;

  /**
  * The Drupal logout URL
  */
  private $drupal_logout_url;

  /**
  * The Drupal login URL
  */
  private $drupal_login_url;

  /**
  * Constructor for this configuration parser.
  *
  * @param array $config  Configuration.
  * @param string $location  The location of this configuration. Used for error reporting.
  */
  public function __construct($config, $location) {
    assert('is_array($config)');
    assert('is_string($location)');

    $this->location = $location;

    /* Parse configuration. */
    $config = SimpleSAML_Configuration::loadFromArray($config, $location);

    $this->debug = $config->getBoolean('debug', FALSE);
    $this->attributes = $config->getArray('attributes', NULL);
    $this->cookie_name = $config->getString('cookie_name', 'drupalauth4ssp');
    $this->drupal_logout_url = $config->getString('drupal_logout_url', NULL);
    $this->drupal_login_url = $config->getString('drupal_login_url', NULL);
  }


  /**
  * Return the debug
  *
  * @param boolean $debug whether or not debugging should be turned on
  */
  public function getDebug() {
    return $this->debug;
  }

  /**
  * Return the attributes
  *
  * @param array $attributes the array of Drupal attributes to use, NULL means use all available attributes
  */
  public function getAttributes() {
  return $this->attributes;
  }

  /**
  * Return the cookie name
  *
  * @param array $cookie_name the name of the cookie
  */
  public function getCookieName() {
  return $this->cookie_name;
  }

  /**
  * Return the Drupal logout URL
  *
  * @param array $drupal_logout_url the URL of the Drupal logout page
  */
  public function getDrupalLogoutURL() {
  return $this->drupal_logout_url;
  }

  /**
  * Return the Drupal login URL
  *
  * @param array $drupal_login_url the URL of the Drupal login page
  */
  public function getDrupalLoginURL() {
  return $this->drupal_login_url;
  }

}
