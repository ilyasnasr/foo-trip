<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('front_destination_list');
//        return $this->render('front/base.html.twig', [
//            'controller_name' => 'FrontController',
//        ]);
    }
}
