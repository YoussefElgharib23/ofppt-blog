<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\ReplyComment;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class PostController extends AbstractController
{
    use TargetPathTrait;

    const PATH = 'client/posts/';

    private $postRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UrlGenerator
     */
    private $urlGenerator;
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * PostController constructor.
     * @param PostRepository $postRepository
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param CommentRepository $commentRepository
     */
    public function __construct(
        PostRepository $postRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CommentRepository $commentRepository
    )
    {
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route ("/home", name="app_blog", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $posts = $this->postRepository->find15LatestPosts();
        // NEED IMPROVEMENTS
        try {
            $count = $this->postRepository->countActivePost();
        } catch (NoResultException | NonUniqueResultException $e) {
        }
        return $this->render(self::PATH . 'index.html.twig', compact('posts', 'count'));
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    private function checkPostScope(Request $request, Post $post): ?RedirectResponse
    {
        if ($post->getStatus() !== 0 || $post->getCategory()->getStatus() !== 0) {
            // CHECK IF THE USER GRANTED THE ROLE TO SEE THE INACTIVE POST
            $this->denyAccessUnlessGranted('view', $post);

            $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('app_show_post', [
                'id' => $post->getId(),
                'slug' => $post->slugify($post->getTitle())
            ]));
        }
        return null;
    }

    /**
     * @Route ("/{slug}-{id}", name="app_show_post", methods={"GET", "POST"}, requirements={"id": "\d+", "slug": "[a-z0-9\-]+"})
     * @param Post $post
     * @param string $slug
     * @param Request $request
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function showPost(
        Post $post,
        string $slug,
        Request $request,
        MessageBusInterface $bus
    ): Response
    {
        // IF THE SLUG WAS EDITED  BY A USER THIS STATEMENT WILL REDIRECT HIM TO THE CORRECT URL
        if ($post->slugify($post->getTitle()) !== $slug) {
            return $this->redirectToRoute("app_show_post", [
                'id' => $post->getId(),
                'slug' => $post->slugify($post->getTitle())
            ]);
        }

        $this->checkPostScope($request, $post);
        $this->saveTargetPath($request->getSession(), 'main', $this->urlGenerator->generate('app_show_post', ['id' => $post->getId(), 'slug' => $slug]));

        /** @var User|null $user */
        $formComment = null;
        $user = $this->getUser();
        if ($user) {
            $comment = (new Comment())->setUser($user)->setPost($post);
            $formComment = $this->createForm(CommentFormType::class, $comment);

            $formComment->handleRequest($request);

            if ($formComment->isSubmitted() && $formComment->isValid()) {
                $this->entityManager->persist($comment);
                $post->addComment($comment);
                $user->addComment($comment);

                if (!$this->isGranted('ROLE_ADMIN')) {
                    $bus->dispatch(new \App\Message\Notification($user->getId(), $post->getId(), 'comment'));
                }
                $this->addFlash('success', 'Your comment was successfully posted !');

                $this->entityManager->flush();

                return $this->redirectToRoute('app_show_post', [
                    'id' => $post->getId(),
                    'slug' => $post->slugify($post->getTitle())
                ]);
            }
        }

        // FIND ALL THE POSTS TO FILTER THE
        $posts = $this->postRepository->findAllPosts();

        // GET ALL THE SAME POST CATEGORY
        $matchedPostsCategory = [];
        /** @var Post $_post */
        foreach ($posts as $_post) {
            if ($_post->getCategory() === $post->getCategory() && $_post->getId() !== $post->getId()) {
                $matchedPostsCategory[] = $_post;
            }
        }

        $count = count($matchedPostsCategory);

        // RELATED POST
        $relatedPost = null;
        if ($count > 1) {
            $id = $count + 1;
            while ($id > $count - 1) {
                $id = mt_rand(0, $count);
            }
            $relatedPost = $matchedPostsCategory[$id];
        } elseif ($count > 0)
            $relatedPost = $matchedPostsCategory[0];

        // MAY LIKE POSTS
        $mayLikePosts = [];
        $__posts = $posts;
        unset($__posts[array_search($relatedPost, $__posts)]);
        unset($__posts[array_search($post, $__posts)]);

        $__posts = array_values($__posts);
        if (count($posts) > 1) {
            $maxCount = 7;
            $countActivePosts = count($posts) - 2;
            if ($countActivePosts < $maxCount) {
                $maxCount = $countActivePosts;
            }
            while (count($mayLikePosts) < $maxCount) {
                $__post = $__posts[mt_rand(0, count($__posts) - 1)];
                if (!in_array($__post, $mayLikePosts)) {
                    $mayLikePosts[] = $__post;
                }
            }
        }

        return $this->render(self::PATH . 'show.html.twig', [
            'post' => $post,
            'relatedPost' => $relatedPost,
            'mayLikePosts' => $mayLikePosts,
            'formComment' => $formComment ? $formComment->createView() : null
        ]);
    }
}
