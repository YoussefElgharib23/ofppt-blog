<?php

namespace App\Controller\AdminControllers;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * Class AdminNotificationController
 * @package App\Controller
 */
class AdminNotificationController extends AbstractController
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/notification/", name="app_admin_notification", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $notifications = $this->notificationRepository->liveNotifications_();
        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->setSeen(true);
        }
        $this->entityManager->flush();
        return $this->render('admin/notifications/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    /**
     * @Route("/admin/notification/delete/{id}", name="app_admin_delete_notification", methods={"DELETE"}, requirements={"id": "\d+"})
     * @param Notification $notification
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteNotification(Notification $notification, Request $request): RedirectResponse
    {
        if ($request->isMethod('DELETE') && $this->isCsrfTokenValid('delete-notification-'.$notification->getId(), $request->request->get('_token'))) {
            $notification->delete();
            $this->addFlash('success', 'The notification was successfully deleted !');
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_admin_notification');
    }
}
