<?php

namespace App\Controller\AdminControllers;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CategoryFormType;
use App\Form\PostFormType;
use App\Repository\CategoryRepository;
use App\Repository\ContactUsRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ContainerRepositoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * Class AdminController
 * @package App\Controller
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
     * @Route ("/", name="app_admin_index", methods={"GET"})
     * @return Response
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
