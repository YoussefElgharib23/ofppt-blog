<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminCommand extends Command
{
    protected static $defaultName = 'make:admin';
    protected static $defaultDescription = 'Create a new admin user';
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManagerInterface;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordEncoderInterface $encoder
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = $this->userRepository->findOneBy(['email' => 'admin@admin.com']);

        if ($user) {
            $io->error('There is already an admin in your database !');

            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail('admin@admin.com')->setUsername('AdminAdmin');
        $user->setPassword($this->encoder->encodePassword($user, 'admin1234'));

        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();

        $io->success('Your admin user was created with success !');

        return Command::SUCCESS;
    }
}