<?php

namespace App\Controller\AdminAjax;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminUserAjaxController
 * @package App\Controller\AdminAjax
 * @IsGranted("ROLE_ADMIN")
 * @Route("/api/admin")
 */
class AdminUserAjaxController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users/bulk", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $users = null;
        $action = null;
        $count = 0;
        try {
            $data = json_decode($request->getContent(), true);
            $users_ids = $data['users'];
            $action = trim(strtolower($data['action']));
            /** @var User[]|null $users */
            $users = $this->userRepository->findAll();
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            /** @var User $user */
            foreach ($users as $user) {
                if (in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
                    if ($user !== $currentUser and in_array($user->getId(), $users_ids))
                    {
                        if ($action === "suspend") {
                            $user->setStatus(false);
                        }
                        elseif ($action === "active")
                        {
                            $user->setStatus(true);
                            $user->restore();
                        }
                        elseif ($action === "delete")
                        {
                            $user->delete();
                            $user->setStatus(false);
                        }
                        $count++;
                    }
                }
                else {
                    if ($user !== $currentUser and in_array($user->getId(), $users_ids) and !in_array('ROLE_ADMIN', $user->getRoles()))
                    {
                        if ($action === "suspend") {
                            $user->setStatus(false);
                        }
                        elseif ($action === "active")
                        {
                            $user->setStatus(true);
                        }
                        elseif ($action === "delete")
                        {
                            $user->delete();
                            $user->setStatus(false);
                        }
                        $count++;
                    }
                }
            }

            $this->entityManager->flush();
        }
        catch(Exception $exception) {
            $this->json([
                'error' => $exception->getMessage()
            ], 500, ['content-type' => 'application/json']);
        }
        return $this->json([
            'message' => 'success',
            'status' => $action,
            'count' => $count
        ]);
    }
}