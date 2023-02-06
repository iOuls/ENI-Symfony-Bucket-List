<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/aboutus', name: 'main_aboutus')]
    public function aboutus(): Response
    {
        $team = json_decode(file_get_contents('../data/team.json'), true);
        return $this->render('main/aboutus.html.twig', [
            'team' => $team,
        ]);
    }
}
