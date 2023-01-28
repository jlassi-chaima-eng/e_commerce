<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
  
    #[Route('/produit_list', name: 'app_produit_list')]
    public function produit_list(ProduitRepository $repository, Request $request): Response
    {       
        return $this->render('produit/index.html.twig', [
            'produits' => $repository->findAll(),
        ]);
    }
    #[Route('/produit/{id}', name: 'produit_show')]
    public function show(ProduitRepository $repository, string $id): Response
    {
        $produit = $repository->find($id);

        if (!$produit) {
            return $this->redirectToRoute('produit');
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

}
