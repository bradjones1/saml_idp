<?php

// This class is not namespaced as simplesamlphp does not namespace module classes.

use Drupal\Core\DrupalKernel;
use Drupal\Core\Url;
use SimpleSAML\Utils\HTTP;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Drupalath authentication source for using Drupal's login page.
 *
 * Original author: SIL International, Steve Moitozo, <steve_moitozo@sil.org>, http://www.sil.org
 * Modified by: Brad Jones, <brad@bradjonesllc.com>, http://bradjonesllc.com
 *
 * This class is an authentication source which is designed to
 * more closely integrate with a Drupal site. It causes the user to be
 * delivered to Drupal's login page, if they are not already authenticated.
 *
 * Original source: http://code.google.com/p/drupalauth/
 */
class sspmod_drupalauth_Auth_Source_External extends SimpleSAML_Auth_Source {

  /**
   * Dependency injection container
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  private $container;

  /**
   * The Drupal application kernel
   *
   * @var DrupalKernel
   */
  private $kernel;

  /**
   * Bootstrap Drupal, e.g., if we're being called from simplesamlphp.
   * @see index.php
   */
  protected function bootstrap() {
    try {
      $this->container = \Drupal::getContainer();
    }
    catch (Exception $e) {
      $finder = new \DrupalFinder\DrupalFinder();
      if ($finder->locateRoot(getcwd())) {
        $classloader = require $finder->getVendorDir() . '/autoload.php';
      }
      else {
        throw new Exception('Could not find Drupal root.');
      }
      DrupalKernel::bootEnvironment($finder->getDrupalRoot());
      $request = Request::createFromGlobals();
      $request->server->set('SCRIPT_FILENAME', '/index.php');
      $this->kernel = DrupalKernel::createFromRequest($request, $classloader, 'prod');
      $this->kernel->prepareLegacyRequest($request);
      $this->container = $this->kernel->getContainer();
    }
    return $this;
  }

  /**
   * Constructor for this authentication source.
   *
   * @param array $info  Information about this authentication source.
   * @param array $config  Configuration.
   */
  public function __construct($info, $config) {
    assert('is_array($info)');
    assert('is_array($config)');

    /* Call the parent constructor first, as required by the interface. */
    parent::__construct($info, $config);

    $this->bootstrap();
  }

  /**
   * Retrieve attributes for the user.
   *
   * @return array|NULL  The user's attributes, or NULL if the user isn't authenticated.
   */
  private function getUser() {
    /* @var \Drupal\Core\Session\AccountInterface $user */
    $user = $this->container->get('current_user')->getAccount();
    if (!$user->isAnonymous()) {
      $site_config = $this->container->get('config.factory')->get('system.site');
      $user_entity = User::load($user->id());
      $attributes = array(
        'uid' => array($user->getUsername()),
        // Return the UUID as it's guaranteed not to change and reduces clashes.
        'uniqueIdentifier' => array('drupal:' . $user_entity->uuid()),
        'displayName' => array($user->getDisplayName()),
        'eduPersonPrincipalName' => array($user->getUsername() . '@drupal.' . $site_config->get('uuid')),
        'mail' => array($user->getEmail()),
      );
      $this->container->get('module_handler')->alter('saml_idp_attributes', $attributes, $user_entity);
      return $attributes;
    }
    return NULL;
  }

  /**
   * Log in using an external authentication helper.
   *
   * @param array &$state  Information about the current authentication.
   */
  public function authenticate(&$state) {
    assert('is_array($state)');

    if ($attributes = $this->getUser()) {
      /*
       * The user is already authenticated.
       *
       * Add the users attributes to the $state-array, and return control
       * to the authentication process.
       */
      $state['Attributes'] = $attributes;
      return;
    }

    /*
     * The user isn't authenticated. We therefore need to
     * send the user to the login page.
     */

    /*
     * First we add the identifier of this authentication source
     * to the state array, so that we know where to resume.
     */
    $state['drupalauth:AuthID'] = $this->authId;

    /*
     * We need to save the $state-array, so that we can resume the
     * login process after authentication.
     *
     * Note the second parameter to the saveState-function. This is a
     * unique identifier for where the state was saved, and must be used
     * again when we retrieve the state.
     *
     * The reason for it is to prevent attacks where the user takes a
     * $state-array saved in one location and restores it in another location,
     * and thus bypasses steps in the authentication process.
     */
    $stateId = SimpleSAML_Auth_State::saveState($state, 'drupalauth:External', TRUE);

    /*
     * Now we generate an URL the user should return to after authentication.
     * We assume that whatever authentication page we send the user to has an
     * option to return the user to a specific page afterwards.
     *
     * Drupal will not redirect to an external URL. So, build a relative one.
     */
    $returnTo = Url::fromRoute('saml_idp.resume', array('State' => $stateId))->toString();
    /*
     * Get the URL of the authentication page.
     */
    $login = Url::fromRoute('user.login')->toString();

    /*
     * The redirect to the authentication page.
     *
     * Note the 'ReturnTo' parameter. This must most likely be replaced with
     * the real name of the parameter for the login page.
     */
    HTTP::redirectTrustedURL($login, array(
      'destination' => $returnTo,
    ));

    /*
     * The redirect function never returns, so we never get this far.
     */
    assert('FALSE');
  }

  /**
   * Resume authentication process.
   *
   * This function resumes the authentication process after the user has
   * entered his or her credentials.
   *
   * @see \Drupal\saml_idp\Resume::resume()
   *
   * @param array &$state  The authentication state.
   */
  public static function resume() {
    /*
     * First we need to restore the $state-array. We should have the identifier for
     * it in the 'State' request parameter.
     *
     * @todo - Handle Request injection if this is on a CLI test?
     */
    $container = \Drupal::getContainer();
    $request = $container->get('request_stack')->getCurrentRequest();
    if (!$stateId = $request->query->get('State')) {
      throw new SimpleSAML_Error_BadRequest('Missing "State" parameter.');
    }
    /*
     * Once again, note the second parameter to the loadState function. This must
     * match the string we used in the saveState-call above.
     */
    $state = SimpleSAML_Auth_State::loadState($stateId, 'drupalauth:External');

    /*
     * Now we have the $state-array, and can use it to locate the authentication
     * source.
     */
    $source = SimpleSAML_Auth_Source::getById($state['drupalauth:AuthID']);
    if ($source === NULL) {
      /*
       * The only way this should fail is if we remove or rename the authentication source
       * while the user is at the login page.
       */
      throw new SimpleSAML_Error_Exception('Could not find authentication source.');
    }

    /*
     * Make sure that we haven't switched the source type while the
     * user was at the authentication page. This can only happen if we
     * change config/authsources.php while an user is logging in.
     */
    if (! ($source instanceof self)) {
      throw new SimpleSAML_Error_Exception('Authentication source type changed.');
    }


    /*
     * OK, now we know that our current state is sane. Time to actually log the user in.
     *
     * First we check that the user is actually logged in, and didn't simply skip the login page.
     */
    $attributes = $source->getUser();
    if ($attributes === NULL) {
      /*
       * The user isn't authenticated.
       *
       * Here we simply throw an exception, but we could also redirect the user back to the
       * login page.
       */
      throw new SimpleSAML_Error_Exception('User not authenticated after login attempt.');
    }

    /*
     * So, we have a valid user. Time to resume the authentication process where we
     * paused it in the authenticate()-function above.
     */

    $state['Attributes'] = $attributes;
    SimpleSAML_Auth_Source::completeAuth($state);

    /*
     * The completeAuth-function never returns, so we never get this far.
     */
    assert('FALSE');
  }


  /**
   * This function is called when the user start a logout operation, for example
   * by logging out of a SP that supports single logout.
   *
   * @param array &$state  The logout state array.
   */
  public function logout(&$state) {
    assert('is_array($state)');

    if ($this->bootstrap()->getUser()) {
      // We may not have a session started, but SessionManager will throw
      // an error if it tries to destroy an uninitialized session.
      $this->container->get('session_manager')->start();
      user_logout();
    }
  }

}
