<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NimesTeamController extends AbstractController
{
    #[Route('/nimes/team', name: 'nimes_team')]
    public function index(): Response
    {
        return $this->render('nimes_team/index.html.twig', [
            'controller_name' => 'NimesTeamController',
        ]);
    }
}
