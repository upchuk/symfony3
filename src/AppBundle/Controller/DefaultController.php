<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends Controller
{
  /**
   * @Route("/", name="homepage")
   */
  public function indexAction(Request $request)
  {
    // replace this example code with whatever you need
    return $this->render('default/index.html.twig', array(
      'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
    ));
  }

  /**
   * @Route("/login", name="login")
   */
  public function loginAction(Request $request)
  {
    $user = $this->getUser();
    if ($user instanceof UserInterface) {
      return $this->redirectToRoute('homepage');
    }

    /** @var AuthenticationException $exception */
    $exception = $this->get('security.authentication_utils')
      ->getLastAuthenticationError();

    return $this->render('default/login.html.twig', [
      'error' => $exception ? $exception->getMessage() : NULL,
    ]);
  }

  /**
   * @Route("/logout", name="logout")
   */
  public function logoutAction(Request $request)
  {
   // Nothing needed.
  }
}
