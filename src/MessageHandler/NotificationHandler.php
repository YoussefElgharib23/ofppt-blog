<?php

namespace App\MessageHandler;

use App\Message\Notification;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Notification $notification)
    {
        $user = $this->userRepository->find($notification->getUserId());
        $post = null;
        if ($notification->getPostId()) {
            $post = $this->postRepository->find($notification->getPostId());
        }
        $_notification = (new \App\Entity\Notification())->setUser($user)->setPost($post)->setType($notification->getType());

        $this->entityManager->persist($_notification);
        $this->entityManager->flush();
    }
}