<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/client/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
    
    #[Route('/client/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
    #[Route('/client/{id}/delete', name: 'app_client_delete', methods :['GET', 'POST'])]
    public function delete(Request $request, Client $client, EntityManagerinterface $entityManager):Response
    {
    if ($this->isCsrfTokenValid(
        'delete'.$client->getId(),
        $request->request->get('_token')
    )) {
        if (!$client->getCommandes()->isEmpty()) {
            $this->addFlash(
                'danger',
                'Impossible de supprimer ce client : il possède déjà des commandes.'
            );

            return $this->redirectToRoute('app_client_index');
        }

        $entityManager->remove($client);
        $entityManager->flush();

        $this->addFlash('success', 'Le client a été supprimé.');
    }

    return $this->redirectToRoute('app_client_index');
}

    #[Route('/client/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    
   
}
