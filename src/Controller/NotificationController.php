<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
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
     * @return Notification[]|array|null
     */
    public function getNotifications(): ?array
    {
        return $this->notificationRepository->liveNotifications();
    }
}
