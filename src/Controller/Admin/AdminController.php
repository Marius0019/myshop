<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class AdminController extends AbstractController
{

    // Gestion produits
    #[Route('/main/modifier/{id}', name:"main_modifier")]
    #[Route('/main/ajout', name:'main_ajout')]
    public function form(Request $globals, EntityManagerInterface $manager, Produit $produit = null,  SluggerInterface $slugger, ProduitRepository $repo) : Response
        {
            $afficheProduit = $repo->findAll();
            if($produit == null)
        {
            
            $produit =  new Produit ;
        }
        $editMode = ($produit->getId() !== null);
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($globals);
        if($form->isSubmitted() && $form->isValid())
        {
            //! Début traitement de l'image 
            $imageFile = $form->get('photo')->getData();
 
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                               
                $produit->setPhoto($newFilename);
            }   
            //!fin du traitement de l'image
            $produit->setDateEnregistrement(new \Datetime);
            $manager->persist($produit);
            $manager->flush();
            if($editMode)
            {
                $this->addFlash('success', "Le t-shirt a bien été modifié");
            }else{

                $this->addFlash('success',
                "Bravo le modele<strong class='text-danger'>{$produit->getTitre()}</strong> est bien ajouté");
            }
            return $this->redirectToRoute('main_gestion');
        }

        
        return $this->render('admin/formProduit.html.twig', [
            'formProduit' => $form,
            'editMode' => $produit->getId() !== null,
            'produits' => $afficheProduit
            
        ]);
    }

        #[Route('/main/supprimer/{id}', name:'main_supprimer')]
    public function supprimer(produit $produit, EntityManagerInterface $manager) : Response
    {
        $manager->remove($produit);
        $manager-> flush();    

        return $this->redirectToRoute('main_gestion');
    }

    #[Route('/main/gestion', name:'main_gestion')]
        public function gestion(ProduitRepository $repo) : Response
        {
            $produit = $repo->findAll();
            return $this->render('admin/gestion.html.twig', [
                'produit' => $produit,
            ]);
        }


       

        
}
