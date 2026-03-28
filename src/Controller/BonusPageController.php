<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BonusPageController extends AbstractController
{
    #[Route('/bonus/page', name: 'bonus_page')]
    public function index(): Response
    {
        return $this->render('bonus_page/index.html.twig', [
            'controller_name' => 'BonusPageController',
        ]);
    }
}
