<?php

namespace App\Controller\AjaxControllers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordUpdateAjaxController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/user/update-password", methods={"POST","GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$this->isCsrfTokenValid('update-password', $data['token']))
                throw new Exception('Invalid csrf protection please refresh your browser !');

            if (!$this->passwordEncoder->isPasswordValid($this->getUser(), $data['oldPassword']))
                throw new Exception('The old password is incorrect !');

            /** @var User $user */
            $user = $this->getUser();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['newPassword']));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your infos has been updated with success !');
        }
        catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
        return $this->json(['message' => 'You have successfully updated your password']);
    }
}
