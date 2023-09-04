<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{


    private $repo;
    private $rs;

    public function __construct(ProduitRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this->repo = $repo;
    }

    //! Faire le add du contrtoller

    public function add($id)
    {
        
        // je sauvegarde l'etat de mon panier en session a l'attribut de session 'cart'
        //Nous allons récupérer une session grâce à la classe RequestStack
        $session = $this->rs->getSession();

        // je récupère l'attribut de session 'cart' s'il existe ou un tableau vide
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        //si le produit existe déjà, j'incrémente sa quantité sinon j'initialise a 1 
        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        }else
        {   $qt++;
             $cart[$id] = 1;
        }
       
        // dans mon tableau $cart, à la case $id je donne la valeur 1
        //indice => valeur // idProduit => QuantitéDuProduitDansLePanier

        $session->set('cart', $cart);
        $session->set('qt', $qt);
    }


    // ! Pour supp une quantité
    
    public function supp($id)
    {
        
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 1);

        if(!empty($cart[$id]))
        {
            
            if($cart[$id] > 2) 
            {
            $qt--;
        }else
        {   
            unset($cart[$id]);
            // unset($qt[$id]);
        }
    }


        $session->set('cart', $cart);
        $session->set('qt', $qt);
    }


    //! Faire le remove du contrtoller

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        // si l'id existe dans mon panier, je le supprime du tableau grace a unset()
        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }

        //gérer l'erreur possible négative.
        if($qt < 0 )
        {
            $qt = 0;
        }

        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    //! Pour faire le CartWithData (la donner) du contrtoller

    public function getCartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        //Je vais créer un nouveau tableau qui contiendra des objets Product et les quantité de chaque objet
        $cartWithData = [];

        //Pour chaque id qui se trouve dans le tableau $cart, on ajoute une case(tableau) dans cartWithData, qui est un tableau multidimensionnel
        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'produit' => $this->repo->find($id),
                'quantity' => $quantity,
                
            ];
        }
        return $cartWithData;

    }
    //! Pour faire le total du contrtoller
    public function getTotal()
    {
        $total = 0; // j'initialise mon total

        foreach($this->getCartWithData() as $item)
        {
            $sousTotal = $item['produit']->getPrix() * $item['quantity'];
            $total += $sousTotal;

        }
        return $total;
    }

}
