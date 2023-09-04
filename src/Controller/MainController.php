<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Commande;
use App\Service\CartService;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ProduitRepository $repo): Response
    {
        $produit = $repo->findAll();
        return $this->render('main/home.html.twig', [
            'produits' => $produit,
        ]);
    }

    // Route de la page mon compte pas terminé
    // #[Route('/main/profil/{id}', name: 'main_profil')]
    // public function profil($id, ProduitRepository $repo): Response
    // {
    //     $produit = $repo->findBy($id);
    //     return $this->render('main/profil.html.twig', [
    //         'produits' => $produit,
    //     ]);
    // }

    
//  Vue de la gestion des commande
        #[Route('/main/show/{id}', name:"main_show")]
        public function show($id, ProduitRepository $repo)
        {
            $produit = $repo->find($id) ;
            return $this->render('main/show.html.twig', [
                'produits' => $produit,
            ]);
        }

// View de la page voir la commande du user
        #[Route('/main/view/{id}', name:"main_view")]
        public function view($id, ProduitRepository $repo)
        {
            $produit = $repo->find($id) ;
            return $this->render('main/view.html.twig', [
                'produits' => $produit,
            ]);
        }

        #[Route('/main/affiche_produit', name: 'affiche_produit')]
        public function index(ProduitRepository $repo): Response
        {
            $produit = $repo->findAll();
            return $this->render('main/index.html.twig', [
                'produits' => $produit,
            ]);
        }


        //  ----------Validation panier
        #[Route('/main/validation', name: 'commande_validation')]
        public function validation(Commande $commande = null, SessionInterface $session , EntityManagerInterface $manager, CommandeRepository $repo, CartService $cs): Response
        {

                $this->denyAccessUnlessGranted('ROLE_USER');

        $cartWithData = $session->get('cartWithData', []);
        
        $cartWithData = $cs->getCartWithData();

        if($cartWithData === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('cart');
        }

        
        foreach($cartWithData as $tab){
            
            $commandeDetails = new Commande();
            $commandeDetails->setUser($this->getUser());
            $commandeDetails->setQuantite($tab['quantity'])
            ->setMontant($tab['quantity'] * $tab['produit']->getPrix())
            ->setDateEnregistrement(new \DateTime())
            ->setProduit($tab['produit'])
            ->setEtat('En cours de traitement');
            
        $manager->persist($commandeDetails);
        }

        $manager->flush();

        $session->remove('cart');

        $this->addFlash('message', 'Votre commande a été créée avec succès');
        return $this->redirectToRoute('main_show');
        
        }
    
        // ---------------Gestion membre -----------------

#[Route('/main/user/modifier/{id}', name:"main_user_modifier")]
#[Route('/main/user/ajout', name:'main_user_ajout')]
public function form(Request $globals, EntityManagerInterface $manager,  UserRepository $repo, User $user =null) : Response
    {
    if($user == null)
        {
            $user =  new User ;
        } 
        $editMode = ($user->getId() !== null);
        $users= $repo->findAll();
      
    
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($globals);
    if($form->isSubmitted() && $form->isValid())
    {
        $user->setDateEnregistrement(new \Datetime);
        $manager->persist($user);
        $manager->flush();
        $this->addFlash("success", "Le membre a bien été enregistré !");
        return $this->redirectToRoute('form_user');
    }

        return $this->render('main/formUser.html.twig', [
            'formUser' => $form,
            'editMode' => $user->getId() !== null,
            'user' => $users

        ]);
    }

#[Route('/main/user/supprimer/{id}', name:'main_user_supprimer')]
public function supprimer(User $user, EntityManagerInterface $manager)
{
    $manager->remove($user);
    $manager-> flush();    
    return $this->redirectToRoute('form_user');
}
}
