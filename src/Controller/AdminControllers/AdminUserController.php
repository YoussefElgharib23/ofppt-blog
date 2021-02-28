<?php

namespace App\Controller\AdminControllers;

use App\Entity\AdminReport;
use App\Entity\User;
use App\Form\AdminReportFormType;
use App\Form\UserCreateFormType;
use App\Repository\CommentRepository;
use App\Repository\DislikeRepository;
use App\Repository\LikeRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Helper\DeleteAssociationEntity;

/**
 * @Route("/admin")
 * @IsGranted ("ROLE_ADMIN")
 * @package App\Controller
 */
class AdminUserController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var LikeRepository
     */
    private $likeRepository;
    /**
     * @var DislikeRepository
     */
    private $dislikeRepository;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        NotificationRepository $notificationRepository,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository,
        DislikeRepository $dislikeRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->notificationRepository = $notificationRepository;
        $this->commentRepository = $commentRepository;
        $this->likeRepository = $likeRepository;
        $this->dislikeRepository = $dislikeRepository;
    }

    /**
     * @Route("/users/", name="app_admin_index_user", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $usersCount = $this->userRepository->count([]);
        $limit = $request->query->getInt('limit', 10);
        $order = $request->query->get('order', 'asc');
        $pages = ceil($usersCount / $limit);
        $currentPage = $request->query->getInt('page', 1);
        $hasRoleSuper = in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles());
        $userQuery = null;
        if ($hasRoleSuper) {
            $userQuery = $this->userRepository->findAllUsersQuery()->orderBy('u.created_at', $order);
        }
        else {
            $userQuery = $this->userRepository->findNonDeletedUser()->orderBy('u.created_at', $order);
        }

        $users = $paginator->paginate(
            $userQuery,
            $currentPage,
            $limit,
        );

        $this->saveTargetPath($request->getSession(), 'main', $request->getUri());

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            'pages' => $pages < 1 ? 1 : intval($pages),
        ]);
    }

    /**
     * @Route("/user/create", name="app_admin_create_user", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        $user = (new User())->setIsCreatedByAdmin(true);
        $form = $this->createForm(UserCreateFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'The user was created with success !');

            return $this->redirectToRoute('app_admin_details_user', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('admin/users/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/details/{id}", name="app_admin_details_user", methods={"GET", "POST"}, requirements={"id": "\d+"})
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function getUserDetails(User $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view_user', $user);
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $adminUserReport = new AdminReport();
        $adminUserReport->setAdminUsername($currentUser->fullName(false))->setUser($user);
        $form = $this->createForm(AdminReportFormType::class, $adminUserReport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->addAdminReport($adminUserReport);
            $this->entityManager->persist($user);
            $this->entityManager->persist($adminUserReport);

            /**
             * You should send an email
             */
            if ($adminUserReport->getNeedToReceive()) {
                // Send an email to user by messenger !!!
            }
            $this->entityManager->flush();
            $this->addFlash('success', 'The report was added to the user with success !');

            return $this->redirectToRoute('app_admin_details_user', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('admin/users/details.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    private function helpOnSuspendOrDelete(User $user)
    {
        $notifications = $this->notificationRepository->findBy(['user' => $user]);
        $comments = $this->commentRepository->findBy(['user' => $user]);
        $likes = $this->likeRepository->findBy(['user' => $user]);
        $dislikes = $this->dislikeRepository->findBy(['user' => $user]);

        DeleteAssociationEntity::deleteAssociation([
            $notifications,
            $comments,
            $likes,
            $dislikes
        ]);
    }

    /**
     * @Route("/suspend/{id}", name="app_admin_suspend_user", methods={"GET", "DELETE"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function suspendUser(
        Request $request,
        User $user
    ): RedirectResponse
    {
        // Don't forget to send the email to user
        $this->denyAccessUnlessGranted('suspend', $user);

        if ($request->isMethod('DELETE') and $this->isCsrfTokenValid('suspend-user', $request->request->get('_token'))) {
            $desc = $request->request->get('description');
            if (trim($desc) === '') { $this->addFlash('error', 'You should provide a description to send it to the user !!'); return $this->redirectToRoute('app_admin_index_user');}
            // Send an email to the user

            $adminReport = (new AdminReport())->setUser($user)->setDescription($desc)->setAdminUsername($this->getUser()->getUsername());
            $this->entityManager->persist($adminReport);
        }
        $user->setStatus(false);
        $this->entityManager->persist($user);

        $this->helpOnSuspendOrDelete($user);

        $this->entityManager->flush();

        $this->addFlash('success', 'The user was successfully suspended !');
        $url = $this->getTargetPath($request->getSession(), 'main');

        return new RedirectResponse($url);
    }

    /**
     *
     * @Route("/user/{id}/active", name="app_admin_active_user", methods={"GET"}, requirements={"id": "\d+"})
     * @param User $user
     * @return RedirectResponse
     */
    public function activeUser(User $user): RedirectResponse
    {
        $user->active();

        $notifications = $this->notificationRepository->findBy(['user' => $user]);
        $comments = $this->commentRepository->findBy(['user' => $user]);
        $likes = $this->likeRepository->findBy(['user' => $user]);
        $dislikes = $this->dislikeRepository->findBy(['user' => $user]);

        DeleteAssociationEntity::restoreAssociation([
            $notifications,
            $comments,
            $likes,
            $dislikes
        ]);

        $this->entityManager->flush();
        $this->addFlash('success', 'The user was activated with success !');
        return $this->redirectToRoute('app_admin_index_user');
    }

    /**
     *
     * @Route("/admin/user/{id}/delete", name="app_admin_delete_user", methods={"DELETE"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        if ($request->isMethod('DELETE') and $this->isCsrfTokenValid('delete-user', $request->request->get('_token'))) {
            $user->delete();
            $this->denyAccessUnlessGranted('remove', $user);
            $this->helpOnSuspendOrDelete($user);
            $this->addFlash('success', 'The user was deleted with success !');

            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_admin_index_user');
    }
}