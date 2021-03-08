<?php

namespace App\Controller\AdminControllers;

use App\Entity\User;
use App\Form\UserProfileAdminFormType;
use App\Form\UserProfileFormType;
use App\Helper\CacheImage;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\ProducerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route("/admin")
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
    /**
     * @var UploaderHelper
     */
    private $helper;
    /**
     * @var ProducerInterface
     */
    private $producer;
    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UploaderHelper $helper,
        ProducerInterface $producer,
        CacheManager $cacheManager
    )
    {
        $this->entityManager = $entityManager;
        $this->helper = $helper;
        $this->producer = $producer;
        $this->cacheManager = $cacheManager;
    }

    /**
     * USER ADMIN PROFILE
     *
     * @Route("/profile", name="app_admin_index_profile", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $formUpdateProfile = $this->createForm(UserProfileAdminFormType::class, $user);
        $formUpdateProfilePic = $this->createForm(UserProfileFormType::class, $user);

        $formUpdateProfile->handleRequest($request);
        $formUpdateProfilePic->handleRequest($request);

        if ($formUpdateProfile->isSubmitted() && $formUpdateProfile->isValid() || $formUpdateProfilePic->isSubmitted() && $formUpdateProfilePic->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Your information was successfully updated !');

            // CacheImage::LiipBackgroundCacheImage($user, $this->helper, $this->producer);

            return $this->redirectToRoute('app_admin_index_profile');
        }
        return $this->render('admin/profile/index.html.twig', [
            'profileForm' => $formUpdateProfile->createView(),
            'formUpdateProfilePic' => $formUpdateProfilePic->createView()
        ]);
    }
}
