<?php

namespace App\DataFixtures;

use App\Entity\AdminReport;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use phpDocumentor\Reflection\Types\This;

class AdminReportsFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = $this->userRepository->findAll();
        /** @var User $user */
        $user = $this->userRepository->find(1);
        for ($i= 0; $i < 5; $i++) {
            $adminReport = (new AdminReport())->setAdminUsername($user->fullName(false))->setUser($faker->randomElement($users))->setDescription($faker->realText(500));

            $manager->persist($adminReport);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return [
            'AdminReports'
        ];
    }
}
