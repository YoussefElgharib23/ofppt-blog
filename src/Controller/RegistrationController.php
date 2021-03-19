<?php

namespace App\Controller;

use App\Message\LoginTrack;
use App\Entity\User;
use App\Message\Notification;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Security\LoginAppAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginAppAuthenticator $authenticator
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function register (
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginAppAuthenticator $authenticator,
        MessageBusInterface $bus
    ): Response
    {
        if ( $this->getUser() ) {
            /** @var User $user */
            $user = $this->getUser();
            $routeName = in_array('ROLE_ADMIN', $user->getRoles()) === true ? 'app_admin_home' : 'app_home';
            return $this->redirectToRoute($routeName);
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setStatus(1);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            $entityManager->flush();

            // generate a signed url and email it to the user
//            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
//                (new TemplatedEmail())
//                    ->from(new Address('noreply@blogs.com', 'Blog admin'))
//                    ->to($user->getEmail())
//                    ->subject('Please Confirm your Email')
//                    ->htmlTemplate('registration/confirmation_email.html.twig')
//            );
            $bus->dispatch(new Notification($user->getId(), null, 'register'));
            $bus->dispatch(new LoginTrack($user->getId()));
            $this->addFlash('success', 'Please check your mail box to confirm your email address');
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     * @param Request $request
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function verifyUserEmail(Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }


        $this->addFlash('success', 'Your email address has been verified.');
        /** @var User $user */
        $user = $this->getUser();
        $bus->dispatch(new Notification($user->getId(), null, 'emailConfirm'));
        return $this->redirectToRoute('app_home');
    }
}