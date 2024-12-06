<?php

namespace App\Controller\Front;

use App\Entity\Destination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/destinations')]
class DestinationController extends AbstractController
{
    #[Route('/', name: 'front_destination_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $destinations = $entityManager->getRepository(Destination::class)->findAll();

        return $this->render('front/destination/list.html.twig', [
            'destinations' => $destinations,
        ]);
    }

    #[Route('/{id}', name: 'front_destination_show')]
    public function show(Destination $destination): Response
    {
        return $this->render('front/destination/show.html.twig', [
            'destination' => $destination,
        ]);
    }
}
