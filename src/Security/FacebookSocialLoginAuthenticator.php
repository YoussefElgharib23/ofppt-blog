<?php

namespace App\Security;

use App\Entity\User;
use App\Message\Notification;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Vich\UploaderBundle\Naming\Base64Namer;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class FacebookSocialLoginAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var UploaderHelper
     */
    private $helper;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ContainerInterface $container,
        MessageBusInterface $bus
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->container = $container;
        $this->bus = $bus;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);

        $user = $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['facebook_id' => $facebookUser->getId()]);

        /** @var User $user */
        if ($user && !$user->isStatus()) {
            $this->container->get('session')->getFlashBag()->add('error', 'Your account has been disabled !');
            return null;
        }

        if ($existingUser) {
            return $existingUser;
        }

        if (!$user instanceof User) $user = new User();
        else return $user;

        $imageName = null;
        if ($facebookUser->getPictureUrl()) {
            $img_file = file_get_contents($facebookUser->getPictureUrl());
            $imageName = $fileName = md5(date('Y-m-d H:i:s:u')).'.jpg';
            file_put_contents("uploads/users/$fileName", $img_file);
        }

        $user->setFirstName($facebookUser->getFirstName());
        $user->setLastName($facebookUser->getLastName());
        $user->setFacebookId($facebookUser->getId());
        $user->setEmail($facebookUser->getEmail());
        $user->setIsVerified(true);
        $user->setImageName($imageName);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->bus->dispatch(new Notification($user->getId(), null, "register"));
        return $user;
    }

    private function getFacebookClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('facebook_main');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->router->generate('app_blog'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        $targetUrl = $this->router->generate('app_blog');

        return new RedirectResponse($targetUrl);
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
