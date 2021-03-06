<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        Security $security
    )
    {
        $this->security = $security;
    }

    use TargetPathTrait;
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @return null|bool
     */
    public function logoutSuspendedOrDeletedUser(): ?bool
    {
        /** @var User| null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser) return null;
        if (
            !$this->security->isGranted('ROLE_ADMIN', $currentUser)
            and !$currentUser->isStatus()
        )
        {
            $this->addFlash('info', 'For security purpose please login again');
            return true;
        }
        return false;
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}