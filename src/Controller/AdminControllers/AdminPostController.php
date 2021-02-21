<?php

namespace App\Controller\AdminControllers;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostEditFormType;
use App\Form\PostFormType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\ProducerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Async\Commands;
use Liip\ImagineBundle\Async\ResolveCache;
use Liip\ImagineBundle\Async\Topics;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route(path="/admin")
 * @IsGranted("ROLE_ADMIN")
 * Class AdminPostController
 * @package App\Controller
 */
class AdminPostController extends AbstractController
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
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/posts/", name="app_admin_index_post", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $currentPage = $request->query->getInt('page', 1);
        $posts = $paginator->paginate(
            $this->postRepository->findAllPostsForAdmin(),
            $currentPage,
            $limit
        );
        return $this->render('admin/posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/create", name="app_admin_create_post", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createPost(Request $request, ProducerInterface $producer): Response
    {
        $form = $categoriesCount = null;
        try {
            $categoriesCount = $this->categoryRepository->count([]);
            /** @var User $user */
            $user = $this->getUser();
            $post = (new Post())->setUser($user);
            $form = $this->createForm(PostFormType::class, $post);

            $this->denyAccessUnlessGranted('create', $post);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($post);
                $resolvedPath = $this->cacheManager->getBrowserPath(parse_url($this->uploaderHelper->asset($post, 'imageFile'), PHP_URL_PATH), 'thumb');
                $post->setImageNameCached($resolvedPath);
                $this->entityManager->persist($post);
                $this->entityManager->flush();
                
                $producer->sendCommand(Commands::RESOLVE_CACHE, new ResolveCache($this->uploaderHelper->asset($post, 'imageFile'), array('thumb')), true);

                $this->addFlash('success', 'The post was created with success !');

                return $this->redirectToRoute('app_admin_create_post');
            }
        }
        catch (Exception $exception)
        {
            $this->addFlash('error', $exception->getMessage());
            return $this->render('admin/posts/create.html.twig', [
                'form' => $form->createView(),
                'categoriesCount' => $categoriesCount
            ]);
        }

        return $this->render('admin/posts/create.html.twig', [
            'form' => $form->createView(),
            'categoriesCount' => $categoriesCount
        ]);
    }

    /**
     * @Route("/post/edit/{slug}-{id}", name="app_admin_edit_post", methods={"GET", "PUT"}, requirements={"id": "\d+", "slug": "[a-z\-]+"})
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function editPost(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('edit', $post);
        $form = $this->createForm(PostEditFormType::class, $post, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $resolvedPath = $this->cacheManager->getBrowserPath(parse_url($this->uploaderHelper->asset($post, 'imageFile'), PHP_URL_PATH), 'thumb');
            $post->setImageNameCached($resolvedPath);
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'The post was updated with success !');

            return $this->redirectToRoute('app_admin_index_post');
        }

        return $this->render("admin/posts/edit.html.twig", [
            'form' => $form->createView(),
            'categoriesCount' => $this->categoryRepository->count([]),
            'post' => $post
        ]);
    }

    /**
     * @Route ("/post/active/{id}", methods={"GET"}, name="app_admin_active_post", requirements={"id": "\d+"})
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function activePost(Request $request, Post $post): RedirectResponse
    {
        $this->denyAccessUnlessGranted('active', $post);
        if ($post->getStatus() == 1) {
            $post->setStatus(0);
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'The post was successfully active !');
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), 'main')) {
            return new RedirectResponse($targetPath);
        }
        return $this->redirectToRoute('app_admin_index_post');
    }

    /**
     * @Route ("/post/{id}/remove", name="app_admin_remove_post", methods={"GET"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function removePost(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('remove', $post);
        if ($post->getDeletedAt() !== null) {
            return $this->redirectToRoute('app_admin_index_post');
        }

        $post->setDeletedAt(new DateTimeImmutable());
        $post->setStatus(2);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->addFlash('success', 'The post was successfully removed');

        if ($registeredRoute = $this->getTargetPath($request->getSession(), 'main')) {
            $generatedUrl = $this->urlGenerator->generate('app_show_post', [
                'id' => $post->getId(),
                'slug' => $post->slugify($post->getTitle())
            ]);
            if ($generatedUrl !== $registeredRoute) {
                return new RedirectResponse($registeredRoute);
            }
            return new RedirectResponse($registeredRoute);
        }

        return $this->redirectToRoute('app_admin_index_post');
    }

    /**
     * @Route ("/post/{id}/delete", name="app_admin_delete_post", methods={"DELETE"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function deletePost(Request $request, Post $post): RedirectResponse
    {
        $this->denyAccessUnlessGranted('delete', $post);
        if ($post->getDeletedAt() === null) {
            return $this->redirectToRoute('app_admin_index_post');
        }
        if ($request->isMethod('DELETE') and $this->isCsrfTokenValid('delete-post-' . $post->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'The post was successfully deleted !');

            if ($path = $this->getTargetPath($request->getSession(), 'main')) {
                return new RedirectResponse($path);
            }
        }

        return $this->redirectToRoute('app_admin_index_post');
    }

    /**
     * @Route("/post/{id}/restore", name="app_admin_restore_post", methods={"GET"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function restore(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('restore', $post);
        if ($post->getDeletedAt() === null) return $this->redirectToRoute('app_admin_index_post');

        $post->setDeletedAt(null);
        $post->setStatus(0);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->addFlash('success', 'The post was successfully restored !');
        if ($targetPath = $this->getTargetPath($request->getSession(), 'main')) {
            $url = $this->generateUrl('app_show_post', [
                'id' => $post->getId(),
                'slug' => $post->slugify($post->getTitle())
            ]);
            if ($url !== $targetPath) {
                return new RedirectResponse($url);
            }
            return new RedirectResponse($targetPath);
        }

        return $this->redirectToRoute('app_admin_index_post');
    }
}