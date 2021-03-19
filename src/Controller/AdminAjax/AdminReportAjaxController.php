<?php

namespace App\Controller\AdminAjax;

use App\Repository\AdminReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminReportAjaxController
 * @package App\Controller\AdminAjax
 * @IsGranted("ROLE_ADMIN")
 */
class AdminReportAjaxController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AdminReportRepository
     */
    private $adminReportRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AdminReportRepository $adminReportRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->adminReportRepository = $adminReportRepository;
    }

    /**
     * Remove the report from the database
     *
     * @Route("/ajax/admin/reports/delete", name="app_admin_ajax_remove_report", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function removeReport(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $report = $this->adminReportRepository->find($data['reportId']);
        $report->delete();
        $this->entityManager->persist($report);
        $this->entityManager->flush();
        return $this->json([
            'message' => 'success'
        ]);
    }
}
