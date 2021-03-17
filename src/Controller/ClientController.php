<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\UserProfileFormType;
use App\Helper\CacheImage;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\ProducerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ClientController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UploaderHelper
     */
    private $helper;

    public function __construct(
        EntityManagerInterface $entityManager,
        UploaderHelper $helper
    )
    {
        $this->entityManager = $entityManager;
        $this->helper = $helper;
    }

    /**
     * @Route ("/profile", name="app_client_profile", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $formUpdateProfile = $this->createForm(UserFormType::class, $user);
        $formUpdateProfilePic = $this->createForm(UserProfileFormType::class, $user);
        $formUpdateProfile->handleRequest($request);
        $formUpdateProfilePic->handleRequest($request);
        if ($formUpdateProfile->isSubmitted() && $formUpdateProfile->isValid() || $formUpdateProfilePic->isSubmitted() && $formUpdateProfilePic->isValid()) {
            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Your profile was successfully updated !');

                CacheImage::LiipBackgroundCacheImage($user, $this->helper, $this->producer);
                return $this->redirectToRoute('app_client_profile');
            }
            catch (Exception $exception) {
                $this->addFlash('error', 'An error has occurred !');
                return $this->redirectToRoute('app_client_profile');
            }
        }
        return $this->render('client/user/profile.html.twig', [
            'profileForm' => $formUpdateProfile->createView(),
            'formUpdateProfilePic' => $formUpdateProfilePic->createView()
        ]);
    }
}