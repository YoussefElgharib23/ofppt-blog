<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminAppFixture extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $user = new User();
        $user->setFirstName('Youssef');
        $user->setLastName('El Gharib');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setStatus(true);
        $user->setGender('male');
        $user->setUsername('Youssef@elgharib2020');
        $user->setEmail('admin@admin.com');
        $user->setPassword($this->encoder->encodePassword($user, 'Youssef@2310'));

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @return string[]
     */
    public static function getGroups(): array
    {
       return [
           'UserAdminAccount'
       ];
    }
}
