<?php

namespace App\Controller\Admin;
use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Repository\ProduitRepository;

class ProduitAdminController extends AbstractController
{
    #[Route('/new_produit', name: 'ajouter_produit')]
    public function index(Request $request, EntityManagerInterface $entityManager ,SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $ImageFile */
            $ImageFile = $form->get('image')->getData();

            // this condition is needed because the 'Image' field is not required
            
            // $output->writeln($ImageFile."cccccc");
            if ($ImageFile) {
                $originalFilename = pathinfo($ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$ImageFile->guessExtension();

                // Move the file to the directory where brochures are stored
              
                    $ImageFile->move(
                        $this->getParameter('ProduitImage_directory'),
                        $newFilename
                    );
               

                // updates the 'Filename' property to store the PDF file name
                // instead of its contents
                $produit->setImage($newFilename);
            }

            // ... persist the $product variable or any other work
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_list');
        }

        return $this->renderForm('produit_admin/new.html.twig', [
            'form' => $form,
        ]);
    
    }
   
    #[Route('/supprimer/{id}', name: 'Supprimer_Produit')]
    public function supprimer(Produit $produit ,ProduitRepository $repository, EntityManagerInterface $entityManager)
    {
        $image = $produit->getImage();
        
        if($image){
            
            
                // On "génère" le chemin physique de l'image
                $nomImage = $this->getParameter("ProduitImage_directory") . '/' . $image;
                
                // On vérifie si l'image existe
                if(file_exists($nomImage)){
                    unlink($nomImage);
                }
            
        }

        $entityManager->remove($produit);
        $entityManager->flush();

        $this->addFlash('message', 'Produit supprimée avec succès');
        return $this->redirectToRoute('app_produit_list');
    }

    #[Route('/edit/{id}', name: 'Edit_Produit')]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        $image = $produit->getImage();
        
        if($image){
            
            
                // On "génère" le chemin physique de l'image
                $nomImage = $this->getParameter("ProduitImage_directory") . '/' . $image;
                
                // On vérifie si l'image existe
                if(file_exists($nomImage)){
                    unlink($nomImage);
                }
            
        }

        if ($form->isSubmitted() && $form->isValid()) {
             /** @var UploadedFile $ImageFile */
             $ImageFile = $form->get('image')->getData();

             // this condition is needed because the 'Image' field is not required
             
             // $output->writeln($ImageFile."cccccc");
             if ($ImageFile) {
                 $originalFilename = pathinfo($ImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$ImageFile->guessExtension();
 
                 // Move the file to the directory where brochures are stored
               
                     $ImageFile->move(
                         $this->getParameter('ProduitImage_directory'),
                         $newFilename
                     );
                
 
                 // updates the 'ImageFilename' property to store the PDF file name
                 // instead of its contents
                 $produit->setImage($newFilename);
             }
 
            $entityManager->flush();
            return $this->redirectToRoute('app_produit_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_admin/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
}