<?php

namespace App\Controller\AdminAjax;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminNotificationAjax
 * @package App\Controller\AdminAjax
 * @IsGranted("ROLE_ADMIN")
 */
class AdminNotificationAjax extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/admin/ajax/notification/read", methods={"POST"})
     */
    public function markAllRead(): JsonResponse
    {
        try {
            $notifications = $this->notificationRepository->findAll();

            foreach ($notifications as $notification) {
                $notification->setSeen(true);
            }

            $this->entityManager->flush();
            return $this->json([
                'message' => 'success'
            ], 200);
        }
        catch (Exception $ex){
            return $this->json([
                'error' => 'An error has occurred !'
            ], 500);
        }
    }
}