<?php

namespace App\Controller;
use App\Entity\DemandeDevis;
use App\Form\DemandeDevisType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/liste', name: 'app_liste')]
    public function liste():Response{
        return $this->render('home/liste.html.twig');
    }
      #[Route('/ajouter', name: 'ajouter_demandeDevis')]
    public function ajouter(Request $request): Response{
            $demandeDevis = new DemandeDevis();
            $form = $this->createForm(DemandeDevisType::class , $demandeDevis);
            $form->handleRequest($request);
            $formData = $form->getData();
                if($form->isSubmitted() && $form->isValid()){

                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($demandeDevis);
                    $em->flush();

                }


        return $this->render('home/ajouter.html.twig');
    }

    
}
