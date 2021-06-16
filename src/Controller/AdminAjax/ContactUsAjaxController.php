<?php

namespace App\Controller\AdminAjax;

use App\Entity\User;
use App\Repository\ContactUsRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ContactUsAjaxController extends AbstractController
{
    /**
     * @var ContactUsRepository
     */
    private $contactUsRepository;
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ContactUsRepository $contactUsRepository,
        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper,
        RouterInterface $router
    ) {
        $this->contactUsRepository = $contactUsRepository;
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->router = $router;
    }

    /**
     * @Route("/admin/api/contact-us-message", methods={"POST"})
     */
    public function getContactMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $contactUs = $this->contactUsRepository->findOneBy(['id' => $id]);
        $contactUs->setFormattedCreatedAt();
        if (null != $contactUs->getImageName()) {
            $path = $this->uploaderHelper->asset($contactUs, 'imageFile');
            $resolvedPath = $this->cacheManager->getBrowserPath(parse_url($path, PHP_URL_PATH), 'thumb');
            $contactUs->setImgTargetPath($resolvedPath);
        }
        if ($contactUs->hasReply()) {
            $contactUs->getReplyContactUs()->getUser()->setFullName();
            $contactUs->getReplyContactUs()->setTwoLatter();
            $contactUs->getReplyContactUs()->setFormattedCreatedAt();
            $contactUs->getReplyContactUs()->setMinCreatedAt();
        }
        /** @var User $cUser */
        $cUser = $this->getUser();

        $delete_link = $this->router->generate('app_admin_delete_contact-us', [
            'id' => $contactUs->getId(),
        ]);

        return $this->json(['message' => $contactUs, 'currentUser' => $cUser->firstTowLatterName(), 'delete' => $delete_link], 200, [], ['groups' => 'contact:ajax']);
    }
}
