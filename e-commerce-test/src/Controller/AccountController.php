<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandeRepository;
use App\Entity\Commande;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\StreamedResponse;
class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {       
        return $this->render('account/index.html.twig', [
        ]);
    
    }
    #[Route('/account/orders', name: 'account_orders')]
    public function showOrders(CommandeRepository $repository): Response
    {
        $orders = $repository->findPaidOrdersByUser($this->getUser());
        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }
    #[Route('/orders', name: 'orders')]
    public function showAllOrders(CommandeRepository $repository): Response
    {
        $orders = $repository->findAll();
        
        
         return $this->render('account/Allorders.html.twig', [
            'orders' => $orders
        ]);
    }
    
    #[Route('/order_to_csv', name: 'order_to_csv')]
    public function order_to_csv(CommandeRepository $repository): Response
    {
        $Commande_Ligne=$repository->findAll();
        
        $response = new StreamedResponse(function () use ($Commande_Ligne) {
            $csv = fopen('php://output', 'w+');
            fputcsv($csv, [
                'id',
                'refernece',
                'prixavecTva',
                'nom',               
                'adresse',
                'country',
                'city',
                'Zipcode',
                
                'indexArticle',
                // 'NbrArticleCmde',
                'IdLigneCmde',
                'NomArticle',
                'PrixArticle',
                'Quantite',
                'QuantiteTotal'
            ],';');
            
            /**
             * @var \Your\Entity\Class $row
             */


            foreach ($Commande_Ligne as $row) {
                foreach($row->getLigneCommandes() as $key=>$Lc)
                
                fputcsv($csv, [
                    $row->getId(),
                    $row->getReference(),
                    $row->getPrixAvecTva(),
                    $row->getUser()->getFirstname(),
                    $row->getUser()->getAdress(),
                    $row->getUser()->getCountry(),
                    $row->getUser()->getCity(),
                    $row->getUser()->getzipcode(),
                    
                    $key,
                    // $key=1+$key,
                    $Lc->getId(),
                    $Lc->getProduit()->getNom(),
                    $Lc->getProduit()->getPu(),
                    $Lc->getQuantite(),
                    $row->getTotalQuantity(),

                ],';' );
            
            }
            fclose($csv);
        });
        
    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="file.csv"');
    $response->headers->set('Cache-Control', 'no-store');
        return $response;


            }
     /**
     * Affiche une commande
     */
    #[Route('/account/order/{reference}', name: 'account_order')]
    public function showOrder(Commande $commande): Response
    {
        if (!$commande || $commande->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('Commande innaccessible');
        }
        return $this->render('account/Show_order.html.twig', [
            'order' => $commande
        ]);
    }
 }
    

