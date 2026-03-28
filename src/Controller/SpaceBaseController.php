<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpaceBaseController extends AbstractController
{
    #[Route('/space/base', name: 'space_base')]
    public function index(): Response
    {
        return $this->render('space_base/index.html.twig', [
            'controller_name' => 'SpaceBaseController',
        ]);
    }
}
