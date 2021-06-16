<?php

namespace App\Controller;

use App\Entity\ContactUs;
use App\Entity\ReplyContactUs;
use App\Entity\User;
use App\Form\ContactUsFormType;
use App\Repository\ContactUsRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ContactUsRepository
     */
    private $contactUsRepository;

    public function __construct(
        PostRepository $postRepository,
        EntityManagerInterface $entityManager,
        ContactUsRepository $contactUsRepository
    ) {
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
        $this->contactUsRepository = $contactUsRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function welcome(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_blog');
        }

        return $this->render('welcome/index.html.twig');
    }

    /**
     * @Route("/terms_of_coniditions", name="app_terms_conditions", methods={"GET"})
     */
    public function termsAndPolicy(): Response
    {
        return $this->render('conditions/termsPolicy.html.twig');
    }

    /**
     * @Route("/contact-us", name="app_contact_us", methods={"GET", "POST"})
     */
    public function contactUs(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            /** @var User | null $currentUser */
            $currentUser = $this->getUser();
            $contactUsObj = new ContactUs();
            $form = $this->createForm(ContactUsFormType::class, $contactUsObj);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($currentUser) {
                    $contactUsObj->setEmail($currentUser->getEmail());
                }
                $this->entityManager->persist($contactUsObj);
                $this->entityManager->flush();

                $this->addFlash('success', 'The message was sent with success !');

                return $this->redirectToRoute('app_contact_us');
            }

            return $this->render('contact/user/contact-us.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        $messages = $this->contactUsRepository->findBy([], ['id' => 'desc']);
        if ($request->isMethod('POST')) {
            $reply = $request->request->get('reply');
            if ('' === trim($reply)) {
                $this->addFlash('error', 'Reply cannot be null');
            } else {
                /** @var User $cu */
                $cu = $this->getUser();
                $reply = (new ReplyContactUs())->setUser($cu);

                for ($i = 0; $i < count($messages); ++$i) {
                    if ($messages[$i]->getId() === intval($request->request->get('_msg-id'))) {
                        $this->get('session')->set('msg-id', $i);

                        $this->denyAccessUnlessGranted('reply', $messages[$i]);

                        $reply->setContactUs($messages[$i]);
                    }
                }

                $reply->setContent($request->request->get('reply'));

                $this->entityManager->persist($reply);
                $this->entityManager->flush();

                $this->addFlash('success', 'The reply was sent with success !');

                return $this->redirectToRoute('app_contact_us');
            }
        }

        return $this->render('contact/contact-us.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route ("/contact-us/{id}/delete", name="app_admin_delete_contact-us", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function deleteContactUs(ContactUs $contactUs): RedirectResponse
    {
        $this->denyAccessUnlessGranted('delete', $contactUs);
        $this->entityManager->remove($contactUs);
        $this->entityManager->flush();

        $this->addFlash('success', 'The message was successfully deleted !');

        return $this->redirectToRoute('app_contact_us');
    }
}
