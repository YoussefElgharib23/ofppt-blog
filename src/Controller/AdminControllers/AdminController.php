<?php

namespace App\Controller\AdminControllers;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\ContactUsRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * Class AdminController
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var ContactUsRepository
     */
    private $contactUsRepository;

    public function __construct(UserRepository $userRepository, PostRepository $postRepository, ContactUsRepository $contactUsRepository)
    {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->contactUsRepository = $contactUsRepository;
    }

    /**
     * @Route ("", name="app_admin_index", methods={"GET"})
     */
    public function index(): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $latestUsers = $this->userRepository->finLatestUser($currentUser);

        $posts = $this->postRepository->finActivePosts();
        usort($posts, function (Post $p1, Post $p2) {
            return $p2->getLikes()->count() > $p1->getLikes()->count();
        });

        $contactUs = $this->contactUsRepository->findMessagesForAdmin();

        return $this->render('admin/index.html.twig', compact('latestUsers', 'posts', 'contactUs'));
    }
}
