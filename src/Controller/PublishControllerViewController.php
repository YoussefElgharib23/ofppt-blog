<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublishControllerViewController extends AbstractController
{
    /**
     * @Route("/mercure")
     * @return Response
     */
    public function route(): Response
    {
        return $this->render('mercure/index.html');
    }
}
