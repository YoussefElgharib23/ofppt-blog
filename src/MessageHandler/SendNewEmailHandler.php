<?php

namespace App\MessageHandler;

use App\Message\SendNewEmail;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;

class SendNewEmailHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    public function __construct(UserRepository $userRepository, EmailVerifier $emailVerifier)
    {
        $this->userRepository = $userRepository;
        $this->emailVerifier = $emailVerifier;
    }

    public function __invoke(SendNewEmail $email)
    {
        $type = $email->getType();
        $user = $this->userRepository->find($email->getUserId());
        if ($type === 'verification') {
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@blogs.com', 'Blog admin'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        }
    }
}