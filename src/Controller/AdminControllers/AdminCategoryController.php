<?php

namespace App\Controller\AdminControllers;

use App\Entity\Category;
use App\Entity\User;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @Route(path="/admin")
 * Class CategoryAdminController
 * @package App\Controller
 */
class AdminCategoryController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/categories/", name="app_admin_index_category", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $currentPage = $request->query->getInt('page', 1);
        $categories = $paginator->paginate(
            $this->categoryRepository->findCategories(),
            $currentPage,
            $limit
        );
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route ("/category/create", name="app_admin_create_category", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createCategory(Request $request): ?Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $category->setSlug();
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'The category was created with success !');

            return $this->redirectToRoute('app_admin_create_category');
        }
        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/category/edit/{id}", name="app_admin_edit_category", methods={"GET", "PATCH"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function editCategory(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category, [
            'method' => 'PATCH'
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'The category was created with success !');

            return $this->redirectToRoute('app_admin_index_category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/categories/{id}/posts", name="app_admin_posts_category", methods={"GET"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function getCategoryPosts(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted('view_category_posts', $category);
        /** @var User $user */
        $user = $this->getUser();

        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->saveTargetPath($request->getSession(), 'main', $this->urlGenerator->generate('app_admin_posts_category', [
                'id' => $category->getId()
            ]));
        }
        $posts = $category->getPosts();
        return $this->render('admin/category/posts.html.twig', [
            'posts' => $posts,
            'category' => $category
        ]);
    }

    /**
     * @Route("/category/active/{id}", name="app_admin_active_category", methods={"GET"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function activeCategory(Request $request, Category $category): RedirectResponse
    {
        $this->denyAccessUnlessGranted('active', $category);
        $category->setStatus(0);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'The category was successfully activated !');
        $url = $this->getTargetPath($request->getSession(), 'main');
        return new RedirectResponse($url);
    }
}   