<?php

namespace App\Controller\AdminControllers;

use App\Entity\Image;
use App\Form\ImageFormType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin/")
 * @IsGranted ("ROLE_ADMIN")
 * Class AdminImageController
 * @package App\Controller
 */
class AdminImageController extends AbstractController
{

    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        ImageRepository $imageRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("images", name="app_admin_index_images", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageFormType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            $this->addFlash('success', 'The image was uploaded !');

            return $this->redirectToRoute('app_admin_index_images');
        }

        if($request->isMethod('POST')) {
            $this->addFlash('error', 'An error has occurred while uploading the file !');
        }

        $images = $this->imageRepository->findBy([], ['created_at' => 'desc']);
        return $this->render('admin/images/index.html.twig', [
            'images' => $images,
            'form' => $form->createView()
        ]);
    }
}
