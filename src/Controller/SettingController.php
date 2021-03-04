<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingsFormType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SettingController extends AbstractController
{
    /**
     * @var SettingRepository
     */
    private $settingRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UploaderHelper
     */
    private $helper;

    /**
     * SettingController constructor.
     * @param SettingRepository $settingRepository
     * @param EntityManagerInterface $entityManager
     * @param UploaderHelper $helper
     */
    public function __construct(
        SettingRepository $settingRepository,
        EntityManagerInterface $entityManager,
        UploaderHelper $helper
    )
    {
        $this->settingRepository = $settingRepository;
        $this->entityManager = $entityManager;
        $this->helper = $helper;
    }

    /**
     * @Route("/admin/settings", name="app_admin_index_settings", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $settings = $this->settingRepository->findOneBy(['id' => 1]) ?? (new Setting());
        $formSettings = $this->createForm(SettingsFormType::class, $settings);

        $formSettings->handleRequest($request);
        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            // do your submission !!!
            $this->entityManager->persist($settings);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your settings was successfully updated !');

            return $this->redirectToRoute('app_admin_index_settings');
        }

        return $this->render('admin/settings/index.html.twig', [
            'form' => $formSettings->createView()
        ]);
    }

    /**
     * @return string|null
     */
    public function getLogoName(): ?string
    {
        $setting = $this->settingRepository->findOneBy(['id' => 1]);
        return $setting ? $this->helper->asset($setting, 'imageFileLogo') : '';
    }

    /**
     * @return string|null
     */
    public function getWelcome(): ?string
    {
        $setting = $this->settingRepository->findOneBy(['id' => 1]);
        return $setting ?  $this->helper->asset($setting, 'imageFileHome') : '';
    }

    /**
     * @return string
     */
    public function appName(): string
    {
        $setting = $this->settingRepository->findOneBy(['id' => 1]);
        return  $setting ? $setting->getApplicationName() : 'App Name';
    }
}
