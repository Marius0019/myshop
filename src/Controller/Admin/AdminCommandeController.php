<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/commande')]
class AdminCommandeController extends AbstractController
{
    #[Route('/', name: 'admin_commande')]
    public function index(CommandeRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Commande::class)->getFieldNames();
        $coms = $repo->findAll();
        return $this->render('admin_commande/commande.html.twig', [
            'coms' => $coms,
            'colonnes' => $colonnes
        ]);
    }
    
    
    #[Route("/admin/commande/modifier/{id}", name:"admin_commande_modifier")]
    public function form(Commande $commande = null, EntityManagerInterface $manager, Request $rq)
    {
        if (!$commande) {
            $commande = new Commande;
            $commande->setDateEnregistrement(new \DateTime());
        }
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {
            {
                $this->addFlash('danger', 'Une période de temps ne peut pas être négative.');
                if ($commande->getId())
                    return $this->redirectToRoute('admin_commande_modifier', [
                        'id' => $commande->getId()
                    ]);
                else
                    return $this->redirectToRoute('admin_commande');
            }
            

            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'La commande a bien été modifiée !');
            return $this->redirectToRoute('gestion_commande');
        }
        return $this->render('admin_commande/gestion_commande.html.twig', [
            'form' => $form,
            'editMode' => $commande->getId() != NULL
        ]);
    }

    
    #[Route("/supprimer/{id}", name:"admin_commande_supprimer")]
    public function supprimer(Commande $commande = null, EntityManagerInterface $manager)
    {
        if ($commande) {
            $manager->remove($commande);
            $manager->flush();
            $this->addFlash('success', 'La commande a bien été supprimée !');
        }
        return $this->redirectToRoute('admin_commande');
    }

    #[Route('/commande/show/{id}', name:'commande_show')]
        public function show($id, CommandeRepository $repo)
        {
            $commande = $repo->find($id) ;
            return $this->render('main/commande_show.html.twig', [
                'produits' => $commande,
              

            ]);
        }
}

