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

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    /**
     * @param Request $request
     * @return AccessToken
     */
    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);

        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['facebook_id' => $facebookUser->getId()]);

        if (!$existingUser)
            $existingUser = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $facebookUser->getEmail()]);

        /** @var User $existingUser */
        if ($existingUser) {
            if (!$existingUser->isStatus() || $existingUser->isDeleted()) {
                throw new AuthenticationException('Your account has been disabled or deleted !');
            }
            return $existingUser;
        }

        $imageName = null;
        if ($facebookUser->getPictureUrl()) {
            $img_file = file_get_contents($facebookUser->getPictureUrl());
            $imageName = $fileName = md5(date('Y-m-d H:i:s:u')).'.jpg';
            file_put_contents("uploads/users/$fileName", $img_file);
        }

        $user = new User();
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

    /**
     * @return OAuth2ClientInterface
     */
    private function getFacebookClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('facebook_main');
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->container->get('session')->getFlashBag()->add('error', $exception->getMessage());
        return new RedirectResponse($this->router->generate('app_login'));
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
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
}
