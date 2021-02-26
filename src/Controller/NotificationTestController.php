<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationTestController extends AbstractController
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(
        NotificationRepository $notificationRepository
    )
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/notification/test")
     * @return Response
     */
    public function index(): Response
    {
        $notifications = $this->notificationRepository->adminNotifications();
        dd($notifications);
        return new Response('Hello');
    }
}
