<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Category[]|array|null
     */
    public function getCategories(): ?array
    {
        return $this->categoryRepository->findBy(['status' => '0']);
    }

    /**
     * @Route("/{slug}/posts", methods={"GET"}, name="app_posts_category", requirements={"slug": "[a-z0-9\-]+"})
     * @param string $slug
     * @param Category $category
     * @return Response
     */
    public function posts(string $slug, Category $category): Response
    {
        if ($slug !== $category->slugify($category->getName())) {
            return $this->redirectToRoute('app_posts_category', [
                'slug' => $category->slugify($category->getName()),
                'id' => $category->getId()
            ]);
        }
        $posts = $category->getActivePosts()->toArray();
        $posts = array_reverse($posts);
        $count = 0;
        $name = $category->getName();
        return $this->render('client/categories/index.html.twig', compact(['count', 'posts', 'name']));
    }
}
