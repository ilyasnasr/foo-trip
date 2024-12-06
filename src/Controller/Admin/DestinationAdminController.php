<?php

namespace App\Controller\Admin;

use App\Entity\Destination;
use App\Form\DestinationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/destinations')]
class DestinationAdminController extends AbstractController
{
    #[Route('/', name: 'admin_destination_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $destinations = $entityManager->getRepository(Destination::class)->findAll();

        return $this->render('admin/destination/list.html.twig', [
            'destinations' => $destinations,
        ]);
    }

    #[Route('/new', name: 'admin_destination_new')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $destination = new Destination();
        $form = $this->createForm(DestinationType::class, $destination);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form, $destination);

            $entityManager->persist($destination);
            $entityManager->flush();

            return $this->redirectToRoute('admin_destination_list');
        }

        return $this->render('admin/destination/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_destination_edit')]
    public function edit(Destination $destination, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DestinationType::class, $destination);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form, $destination);

            $entityManager->flush();

            return $this->redirectToRoute('admin_destination_list');
        }

        return $this->render('admin/destination/edit.html.twig', [
            'form' => $form->createView(),
            'destination' => $destination,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_destination_delete', methods: ['POST'])]
    public function delete(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $destination->getId(), $request->request->get('_token'))) {
            $entityManager->remove($destination);
            $entityManager->flush();

            $this->addFlash('success', 'Destination deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('admin_destination_list');
    }

    private function handleImageUpload($form, Destination $destination): void
    {
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $slugifyName = $this->slugify($destination->getName());

            $imagesDirectory = $this->getParameter('images_directory');
            if ($destination->getImage()) {
                $oldImagePath = $imagesDirectory . '/' . $destination->getImage();
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $imagesDirectory . '/destinations/' . $slugifyName,
                $newFilename
            );
            $destination->setImage('destinations/' . $slugifyName . '/' . $newFilename);
        }
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        return preg_replace('/[^a-z0-9]+/i', '-', $text);
    }


}