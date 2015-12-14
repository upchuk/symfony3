<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FormAuthenticator extends AbstractGuardAuthenticator
{

  /**
   * @var \Symfony\Component\Routing\RouterInterface
   */
  private $router;

  /**
   * Default message for authentication failure.
   *
   * @var string
   */
  private $failMessage = 'Invalid credentials';

  /**
   * Creates a new instance of FormAuthenticator
   */
  public function __construct(RouterInterface $router) {
    $this->router = $router;
  }

  /**
   * {@inheritdoc}
   */
  public function getCredentials(Request $request)
  {
    if ($request->getPathInfo() != '/login' || !$request->isMethod('POST')) {
      return;
    }

    return array(
      'username' => $request->request->get('username'),
      'password' => $request->request->get('password'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getUser($credentials, UserProviderInterface $userProvider)
  {
    if (!$userProvider instanceof InMemoryUserProvider) {
      return;
    }

    try {
      return $userProvider->loadUserByUsername($credentials['username']);
    }
    catch (UsernameNotFoundException $e) {
      throw new CustomUserMessageAuthenticationException($this->failMessage);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function checkCredentials($credentials, UserInterface $user)
  {
    if ($user->getPassword() === $credentials['password']) {
      return true;
    }
    throw new CustomUserMessageAuthenticationException($this->failMessage);
  }

  /**
   * {@inheritdoc}
   */
  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
  {
    $url = $this->router->generate('homepage');
    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
  {
    $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
    $url = $this->router->generate('login');
    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function start(Request $request, AuthenticationException $authException = null)
  {
    $url = $this->router->generate('login');
    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsRememberMe()
  {
    return false;
  }
}