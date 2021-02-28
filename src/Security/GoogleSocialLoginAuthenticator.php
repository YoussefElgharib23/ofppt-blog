<?php

namespace App\Security;

use App\Entity\User;
use App\Message\Notification;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
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

class GoogleSocialLoginAuthenticator extends SocialAuthenticator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * GoogleSocialLoginAuthenticator constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param RouterInterface $router
     * @param ClientRegistry $clientRegistry
     * @param ContainerInterface $container
     * @param MessageBusInterface $bus
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        RouterInterface $router,
        ClientRegistry $clientRegistry,
        ContainerInterface $container,
        MessageBusInterface $bus
    )
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->clientRegistry = $clientRegistry;
        $this->container = $container;
        $this->bus = $bus;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

        $existingUser = $this->userRepository->findOneBy(['googleId' => $googleUser->getId()]);
        if (!$existingUser) {
            $existingUser = $this->userRepository->findOneBy(['email' => $googleUser->getEmail()]);
        }

        /** @var User $user */
        if ($existingUser && !$existingUser->isStatus() && $existingUser->isDeleted()) {
            $this->container->get('session')->getFlashBag()->add('error', 'Your account has been disabled !');
            return null;
        }

        if ($existingUser) return $existingUser;

        $user = new User();
        $user->setGoogleId($googleUser->getId());
        $user->setFirstName($googleUser->getFirstName());
        $user->setLastName($googleUser->getLastName());
        $user->setEmail($googleUser->getEmail());
        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->bus->dispatch(new Notification($user->getId(), null, "register"));

        return $user;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        $targetUrl = $this->router->generate('app_blog');
        return new RedirectResponse($targetUrl);
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getGoogleClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('google');
    }
}
