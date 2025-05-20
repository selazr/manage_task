<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\LoginFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactory): Response
    {
        $user = new User();
        $loginForm = $formFactory->create(LoginFormType::class, $user);

    return $this->render('login/index.html.twig', [
        'loginForm' => $loginForm->createView(),
        'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
