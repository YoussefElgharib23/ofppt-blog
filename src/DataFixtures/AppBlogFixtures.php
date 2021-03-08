<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Post;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Enqueue\Client\ProducerInterface;
use Faker\Factory;
use Liip\ImagineBundle\Async\Commands;
use Liip\ImagineBundle\Async\ResolveCache;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AppBlogFixtures extends Fixture implements FixtureGroupInterface
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
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * AppBlogFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param ImageRepository $imageRepository
     * @param ContainerInterface $container
     * @param CacheManager $cacheManager
     * @param UploaderHelper $uploaderHelper
     * @param UserRepository $userRepository
     * @param ProducerInterface $producer
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        ImageRepository $imageRepository,
        ContainerInterface $container,
        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper,
        UserRepository $userRepository,
        ProducerInterface $producer
    )
    {
        $this->encoder = $encoder;
        $this->imageRepository = $imageRepository;
        $this->container = $container;
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->userRepository = $userRepository;
        $this->producer = $producer;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->userRepository->find(1);

        $images = $this->imageRepository->findAll();

        $faker = Factory::create();
        $categories = [];

        for ($i = 0; $i < 50; $i++) {
            $category = new Category();

            $category->setName($faker->sentence(1, true))->setSlug();
            $manager->persist($category);

            $categories[] = $category;
        }

        $faker = Factory::create();
        for ($i = 0; $i < 200; $i++) {
            $post = new Post();

            /** @var Image $image */
            $image = $faker->randomElement($images)->getImageName();

            $post
                ->setTitle($faker->sentence(2))
                ->setStatus(mt_rand(0, 1))
                ->setDescription($faker->realText(1000))
                ->setCategory($faker->randomElement($categories))
                ->setUser($user)
                ->setImageName($image)
            ;

            $this->producer->sendCommand(Commands::RESOLVE_CACHE, new ResolveCache($this->uploaderHelper->asset($post, 'imageFile'), array('thumb')));


            $manager->persist($post);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [
            'BlogApp',
        ];
    }
}
