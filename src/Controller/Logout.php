<?php

namespace Drupal\saml_idp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Drupal\Core\Url;

class Logout extends ControllerBase {

  // The URL generator service.
  protected $urlGenerator;

  // The translation service.
  protected $translation;

  // Form Builder service.
  protected $formBuilder;

  function __construct(UrlGeneratorInterface $urlGenerator, TranslationInterface $translation, FormBuilderInterface $formBuilder) {
    $this->urlGenerator = $urlGenerator;
    $this->translation = $translation;
    $this->formBuilder = $formBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('url_generator'),
      $container->get('string_translation'),
      $container->get('form_builder')
    );
  }
  public function logoutSSO() {
    $serviceUrl = \SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler()->getGenerated('SingleLogoutService', 'saml20-idp-hosted');
    $urlReturnTo = $this->urlGenerator->generateFromRoute('saml_idp.logout_complete');
    // SimpleSAMLphp will call the \sspmod_drupalauth_Auth_Source_External::logout method.
    $url = Url::fromUri($serviceUrl, ['query' => ['ReturnTo' => $urlReturnTo]])->toUriString();
    return new RedirectResponse($url);
  }

  public function logoutDrupal() {
    drupal_set_message($this->translation->translate('You have been logged out. Use the form below to log back in.'));
    return $this->formBuilder->getForm('Drupal\user\Form\UserLoginForm');
  }
}
