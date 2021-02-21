<?php

namespace App\Controller\PostAjaxControllers;

use App\Entity\Dislike;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\DislikeRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @IsGranted ("ROLE_USER")
 */
class LikeDislikeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LikeRepository
     */
    private $likeRepository;
    /**
     * @var DislikeRepository
     */
    private $dislikeRepository;

    /**
     * LikeDislikeController constructor.
     * @param EntityManagerInterface $entityManager
     * @param LikeRepository $likeRepository
     * @param DislikeRepository $dislikeRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LikeRepository $likeRepository,
        DislikeRepository $dislikeRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->likeRepository = $likeRepository;
        $this->dislikeRepository = $dislikeRepository;
    }

    /**
     * @Route("/post/like/{id}", name="app_user_like_post", methods={"POST"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function likePost(Request $request, Post $post): Response
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $data = json_decode($request->getContent(), true);
            $csrf_token = $data['csrf'];
            if (!$this->isCsrfTokenValid('ajax', $csrf_token)) {
                throw new InvalidCsrfTokenException();
            }
            $like = (new Like())->setUser($user)->setPost($post);
            $likeType = '>1';
            $isDisliked = $post->isDislikedByUser($user);
            if ($post->isLikedByUser($user)) {
                $like = $this->likeRepository->findOneBy(['user' => $user, 'post' => $post]);
                $post->removeLike($like);
                $this->entityManager->remove($like);
                $likeType = '<1';
            } else {
                $post->addLike($like);
                $this->entityManager->persist($like);
            }
            if ($isDisliked) {
                $dislike = $this->dislikeRepository->findOneBy(['user' => $user, 'post' => $post]);
                $post->removeDislike($dislike);
                $this->entityManager->remove($dislike);
            }
            $this->entityManager->flush();
        } catch (InvalidCsrfTokenException $exception) {
            return $this->json([
                'error' => $exception->getMessage(),
            ], 500, ['content-type' => 'application/json']);
        } catch (Exception $exception) {
            return $this->json([
                'error' => 'an error has occurred !'
            ], 500, ['Content-Type' => 'application/json']);
        }
        return $this->json([
            'message' => 'the like was successfully added !',
            'likeCount' => $post->getActiveUserLikes()->count(),
            'likeType' => $likeType,
            'dislikeCount' => $post->getActiveDislike()->count(),
            'isDisliked' => $isDisliked
        ]);
    }

    /**
     * @Route("/post/dislike/{id}", name="app_user_dislike_post", methods={"POST"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function dislikePost(Request $request, Post $post): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $data = json_decode($request->getContent(), true);
            $csrf_token = $data['csrf'];
            if (!$this->isCsrfTokenValid('ajax', $csrf_token)) {
                throw new InvalidCsrfTokenException();
            }
            $dislikeType = '>1';
            $dislike = (new Dislike())->setUser($user)->setPost($post);
            $isLiked = $post->isLikedByUser($user);
            if ($post->isDislikedByUser($user)) {
                $dislike = $this->dislikeRepository->findOneBy(['user' => $user, 'post' => $post]);
                $post->removeDislike($dislike);
                $this->entityManager->remove($dislike);
                $dislikeType = '<1';
            } else {
                $post->addDislike($dislike);
                $this->entityManager->persist($dislike);
            }
            if ($isLiked) {
                $like = $this->likeRepository->findOneBy(['user' => $user, 'post' => $post]);
                $post->removeLike($like);
                $this->entityManager->remove($like);
            }
            $this->entityManager->flush();
        } catch (InvalidCsrfTokenException $exception) {
            return $this->json([
                'error' => $exception->getMessage()
            ], 500, ['content-type' => 'application/json']);
        } catch (Exception $exception) {
            return $this->json([
                'error' => 'An error has occurred !'
            ], 500, ['content-type' => 'application/json']);
        }
        return $this->json([
            'message' => 'the dislike was successfully added !',
            'likeCount' => $post->getActiveUserLikes()->count(),
            'dislikeType' => $dislikeType,
            'dislikeCount' => $post->getActiveDislike()->count(),
            'isLiked' => $isLiked
        ]);
    }

}