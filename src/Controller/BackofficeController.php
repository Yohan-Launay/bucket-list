<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin_')]
class BackofficeController extends AbstractController
{
    #[Route('/customer', name: 'customer')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backoffice/index.html.twig', [
            'customers' => $userRepository->findAll()
        ]);
    }
}
