<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use function filter_var;

/**
 * @Route ("/api")
 * Class AjaxController
 */
class AjaxController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        PostRepository $postRepository,
        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper
    ) {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
        $this->postRepository = $postRepository;
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * TO VERIFY THE EMAIL AND USERNAME IN THE DB.
     *
     * @Route("/verify_credentials", name="app_ajax_verify_at_register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyAtRegister(Request $request): JsonResponse
    {
        $request->headers->set('Access-Control-Allow-Origin', '*');
        $data = json_decode($request->getContent(), true);

        try {
            if (!$data['username'] && !$data['email']) {
                throw new Exception('Missed fields ! Please try to reload your browser');
            }
            $username = $data['username'];
            $email = strtolower(trim($data['email']));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('The email format is not valid !');
            }

            $user = $this->userRepository->findOneBy(['username' => $username]);
            if ($user) {
                throw new Exception('It seems that the username is taken by another user !');
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);
            if ($user) {
                throw new Exception('The email is taken by another user !');
            }

            return $this->json([
                'message' => 'Success',
            ]);
        } catch (Exception $exception) {
            return $this->json([
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * VERIFY THE EMAIL IF THE USER EXISTS.
     *
     * @Route ("/verify_credentials_login", name="app_ajax_verify_at_login", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyAtLogin(Request $request): JsonResponse
    {
        $request->headers->set('Access-Control-Allow-Origin', '*');

        try {
            $data = json_decode($request->getContent(), true);

            if (!array_key_exists('password', $data) || !array_key_exists('credentials', $data)) {
                throw new Exception('Missing fields !');
            }

            $credentials = $data['credentials'];
            $password = $data['password'];

            $founded = true;
            $user = null;
            if (null === $user) {
                if (filter_var($credentials, FILTER_VALIDATE_EMAIL)) {
                    $user = $this->userRepository->findOneBy(['email' => $credentials]);
                } else {
                    $user = $this->userRepository->findOneBy(['username' => $credentials]);
                }

                if (null === $user) {
                    $founded = false;
                }
            }

            /** @var User $user */
            if ($user && !$this->encoder->isPasswordValid($user, $password)) {
                $founded = false;
            }

            if (!$founded) {
                throw new Exception('Invalid Credentials !');
            }
            if (!$user->isStatus()) {
                throw new CustomUserMessageAuthenticationException('Your account is suspended or deactivated ! Check your mail box to know more');
            }
        } catch (CustomUserMessageAuthenticationException | Exception $exception) {
            return $this->json([
                'error' => $exception->getMessage(),
            ]);
        }

        return $this->json(['message' => 'success']);
    }

    /**
     * @Route ("/load_more", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function loadMorePosts(Request $request): JsonResponse
    {
        $request->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $data = json_decode($request->getContent(), true);
            $id = $data['latestPost'];
            $posts = $this->postRepository->findInfPost($id);
            $returnPosts = [];
            foreach ($posts as $post) {
                /** @var Post $post */
                $path = $this->uploaderHelper->asset($post, 'imageFile');
                $resolvedPath = $this->cacheManager->getBrowserPath($path, 'thumb');
                $post->setImageNameCached($resolvedPath);
                $post->getUser()->setFullName();
                $returnPosts[] = $post;
            }

            $morePosts = count($this->postRepository->findInfPost($posts[count($posts) - 1]->getId())) > 0;

            return $this->json([
                'posts' => $returnPosts,
                'morePosts' => $morePosts,
            ], 200, [], ['groups' => 'ajax:posts']);
        } catch (Exception $exception) {
            return $this->json([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}