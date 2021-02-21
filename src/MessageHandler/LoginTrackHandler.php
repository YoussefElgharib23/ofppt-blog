<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Entity\UserLoginLogs;
use App\Message\LoginTrack;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LoginTrackHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;   
    }

    public function __invoke(LoginTrack $login)
    {
        /** @var User $user */
        $user = $this->userRepository->find($login->getUserId());
        $userLogin = new UserLoginLogs();
        $userLogin->setUser($user)->setLastLogin(new \DateTimeImmutable());
        $this->entityManager->persist($userLogin);
    }
}