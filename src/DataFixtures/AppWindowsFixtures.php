<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppWindowsFixtures
 * @package App\DataFixtures
 */
class AppWindowsFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * AppWindowsFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param ImageRepository $imageRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        ImageRepository $imageRepository,
        UserRepository $userRepository
    )
    {
        $this->encoder = $encoder;
        $this->imageRepository = $imageRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = [];
        $categories = [];
        $posts = [];
        $images = $this->imageRepository->findAll();

        // Create users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($this->encoder->encodePassword($user, $faker->password))
            ;
            $users[] = $user;
            $manager->persist($user);
        }

        // Generate Categories
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->name)->setSlug();
            $categories[] = $category;
            $manager->persist($category);
        }

        // Generate Posts
        for ($i = 0; $i < 200; $i++) {
            $post = new Post();
            /** @var Image $image */
            $image = $faker->randomElement($images)->getImageName();

            $post
                ->setTitle($faker->sentence(2))
                ->setStatus(mt_rand(0, 1))
                ->setDescription($faker->realText(1000))
                ->setCategory($faker->randomElement($categories))
                ->setUser($this->userRepository->find(1))
                ->setImageName($image)
            ;

            $posts[] = $post;
            $manager->persist($post);
        }

        // Generate Likes
        foreach ($posts as $post) {
            for ($i = 0; $i < $faker->numberBetween(0, 40); $i++) {
                $like = new Like();
                $like->setPost($post)->setUser($faker->randomElement($users));
                /** @var Post $post */
                $post->addLike($like);
                $manager->persist($like);
            }
        }

        // Generate Comments
        foreach ($posts as $post) {
            for ($i = 0; $i < $faker->numberBetween(0, 40); $i++) {
                $comment = new Comment();
                $comment->setPost($post)->setUser($faker->randomElement($users))->setComment($faker->realText(50));
                /** @var Post $post */
                $post->addComment($comment);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public static function getGroups(): array
    {
        return [
            'BlogAppWindows'
        ];
    }
}
