<?php

namespace App\Controller\AdminControllers;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\UserProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/")
 * @IsGranted ("ROLE_ADMIN")
 * Class AdminProfileController
 * @package App\Controller
 */
class AdminProfileController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * USER ADMIN PROFILE
     *
     * @Route("profile", name="app_admin_index_profile", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $formUpdateProfile = $this->createForm(UserFormType::class, $user);
        $formUpdateProfilePic = $this->createForm(UserProfileFormType::class, $user);
        $formUpdateProfile->handleRequest($request);
        $formUpdateProfilePic->handleRequest($request);
        if ( $formUpdateProfile->isSubmitted() && $formUpdateProfile->isValid() || $formUpdateProfilePic->isSubmitted() && $formUpdateProfilePic->isValid() ) {
            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Your information was successfully updated !');
                return $this->redirectToRoute('app_admin_index_profile');
            }
            catch (Exception $exception) {
                return $this->render('admin/profile/index.html.twig', [
                    'errorProfileUpdate' => 'An error has occurred !',
                    'profileForm' => $formUpdateProfile->createView(),
                    'formUpdateProfilePic' => $formUpdateProfilePic->createView()
                ]);
            }
        }
        return $this->render('admin/profile/index.html.twig', [
            'profileForm' => $formUpdateProfile->createView(),
            'formUpdateProfilePic' => $formUpdateProfilePic->createView()
        ]);
    }
}
