<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        $jsonFilePath = $this->getParameter('kernel.project_dir') . '/data/team.json';
        $jsonContent = file_get_contents($jsonFilePath);
        $teamData = json_decode($jsonContent, true);

        return $this->render('about/index.html.twig', compact('teamData'));
    }
}
