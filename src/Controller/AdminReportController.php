<?php

namespace App\Controller;

use App\Entity\AdminReport;
use App\Entity\User;
use App\Repository\AdminReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminReportController extends AbstractController
{
    const ACTIONS = [
        'suspend',
        'delete'
    ];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AdminReportRepository
     */
    private $adminReportRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AdminReportRepository $adminReportRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AdminReportRepository $adminReportRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->adminReportRepository = $adminReportRepository;
    }

    /**
     * @Route("/admin/reports", name="app_admin_index_reports", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $reports = $this->adminReportRepository->getReportsForAdmin();
        return $this->render('admin/reports/index.html.twig', compact('reports'));
    }

    /**
     * @Route("/admin/report/{id}/remove", name="app_admin_remove_report", methods={"DELETE"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param AdminReport $report
     * @return RedirectResponse
     */
    public function removeTheReport(Request $request, AdminReport $report): RedirectResponse
    {
        if (
            $request->isMethod('DELETE') and
            $this->isCsrfTokenValid('report-remove-' . $report->getId(), $request->request->get('_token'))
        )
        {
            $report->delete();
            $this->entityManager->persist($report);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_admin_index_reports');
    }

    /**
     * Handle the thing of send an email to user and suspend it
     *
     * @return RedirectResponse
     */
    public function reportUserBeforeSuspend(): RedirectResponse
    {
        // Get the user

        // Send the report

        // then suspend it
        return $this->redirectToRoute('app_admin_index_user');
    }
}
