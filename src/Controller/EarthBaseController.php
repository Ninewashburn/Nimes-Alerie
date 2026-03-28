<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EarthBaseController extends AbstractController
{
    #[Route('/earth/base', name: 'earth_base')]
    public function index(): Response
    {
        return $this->render('earth_base/index.html.twig', [
            'controller_name' => 'EarthBaseController',
        ]);
    }
}
