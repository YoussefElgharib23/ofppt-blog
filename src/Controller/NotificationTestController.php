<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationTestController extends AbstractController
{
    /**
     * @Route("/notification/test/{id}", name="notification_test")
     * @param User $user
     * @param NotificationRepository $notificationRepository
     * @return Response
     */
    public function index(User $user, NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findLikeNotificationByUser($user->getId());
        dd($notifications);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/NotificationTestController.php',
        ]);
    }
}
