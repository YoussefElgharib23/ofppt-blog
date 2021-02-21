<?php

namespace App\Controller\AdminControllers;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryFormType;
use App\Form\PostFormType;
use App\Repository\CategoryRepository;
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
     * @Route ("/", name="app_admin_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
