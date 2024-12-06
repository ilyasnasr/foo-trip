<?php

namespace App\Controller\Api;

use App\Entity\Destination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/destinations')]
class DestinationApiController extends AbstractController
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = 'uploads/images';
    }


    #[Route('/', name: 'api_destination_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Destination::class);
        $name = $request->query->get('name');

        $queryBuilder = $repository->createQueryBuilder('d');

        if ($name) {
            $queryBuilder->andWhere('d.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        $destinations = $queryBuilder->getQuery()->getResult();


        $data = array_map(fn(Destination $destination) => $destination->toArray($request->getSchemeAndHttpHost(), $this->uploadDir), $destinations);
        return $this->json($data);
    }

    #[Route('/{id}', name: 'api_destination_show', methods: ['GET'])]
    public function show(Request $request, Destination $destination): JsonResponse
    {
        return $this->json($destination->toArray($request->getSchemeAndHttpHost(), $this->uploadDir));
    }
}
