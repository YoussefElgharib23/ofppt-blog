<?php

namespace App\Controller\AdminControllers;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class AdminCommentController
 * @package App\Controller
 */
class AdminCommentController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * AdminCommentController constructor.
     * @param EntityManagerInterface $entityManager
     * @param NotificationRepository $notificationRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository,
        CommentRepository $commentRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/admin/comments", name="app_admin_index_comments", methods={"GET"})
     */
    public function index(): Response
    {
        $comments = $this->commentRepository->findAllTheComments();
        return $this->render('admin/comments/index.html.twig', compact('comments'));
    }

    /**
     * @Route("/remove/comment/{id}", methods={"DELETE"}, name="app_admin_remove_comment", requirements={"id": "\d+"})
     * @param Request $request
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function removeComment(
        Request $request,
        Comment $comment
    ): RedirectResponse
    {
        $this->denyAccessUnlessGranted('remove', $comment);
        if (
            $request->isMethod("DELETE")
            and $this->isCsrfTokenValid('delete-comment-' . $comment->getId(), $request->request->get('_token'))
        ) {
            $user = $comment->getUser();
            $notifications = $user->getNotifications();
            foreach ($notifications as $notification) {
                if ($notification->getType() === 'comment') {
                    $notification->delete();
                    $this->entityManager->persist($notification);
                }
            }
            $comment->delete();
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            $this->addFlash('success', 'The comment has been deleted with success !');

            if ($url = $this->getTargetPath($request->getSession(), 'main')) {
                return new RedirectResponse($url);
            }

            $this->addFlash('success', 'The comment was removed with success !');

            return $this->redirectToRoute('app_admin_index_comments');
        }

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/admin/comment/{id}/delete", name="app_admin_delete_comment", methods={"DELETE"})
     * @param Request $request
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function deleteComment(Request $request, Comment $comment): RedirectResponse
    {
        $this->denyAccessUnlessGranted('delete', $comment);
        if (
            $request->isMethod('DELETE')
            and $this->isCsrfTokenValid('delete-comment-' . $comment->getId(), $request->request->get('_token'))
        ) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
            $this->addFlash('success', 'The comment was successfully deleted !');
        }
        return $this->redirectToRoute('app_admin_index_comments');
    }

    /**
     * @Route("/admin/comment/{id}/restore", name="app_admin_restore_comment", methods={"GET"})
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function restoreComment(
        Comment $comment
    ): RedirectResponse
    {
        $this->denyAccessUnlessGranted('restore', $comment);
        $comment->restore();
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_admin_index_comments');
    }
}
