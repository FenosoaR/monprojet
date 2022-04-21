<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ItemController extends AbstractController
{
    #[Route('/item', name: 'app_item')]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

    #[Route('/item/add', name: 'item_add')]
    public function itemAdd(Request $request , SluggerInterface $slugger):Response{

        $item = new Item();
        $form = $this->createForm(ItemType::class , $item);

        $form->handleRequest($request);
        $formData = $form->getData();

            if($form->isSubmitted() && $form->isValid() ){

              $image = $form->get('image')->getData();
              $originalFileName = pathinfo($image->getClientOriginalName() , PATHINFO_FILENAME);
              $safeFileName = $slugger->slug($originalFileName);
                $newFileName =  $safeFileName.'-'.uniqid().$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFileName
                    );
                 } catch (FileException $e) {
                     die($e->getMessage());
                     
                 }
                 $item->setImageName($newFileName);
                 
                $item->setSlug(
                    $slugger->slug(strtolower($formData->getTitre())."-".uniqid())
                );

                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();

                return $this->redirectToRoute('item_all');

            }

        return $this->render('item/add.html.twig' , [
            'formulaire' => $form->createView()
        ]);

    }

    #[Route('/item/all', name: 'item_all')]
    public function  itemAll() : Response {
        $repo = $this->getDoctrine()->getRepository(Item::class);
        $item = $repo->findAll();


        return $this->render('item/all.html.twig' , [
            'items' => $item
        ]);
    }

    #[Route('/item/modify/{id}', name: 'item_modify')]
    public function itemModify(Request $request,$id) : Response {

        $em = $this->getDoctrine()->getManager();
        $itemData = $em->getRepository(Item::class)->find($id);
        $item = new Item();
        $form = $this->createForm(ItemType::class , $item);
        $form->handleRequest($request);

        $formData = $form->getData();
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $item = $em->getRepository(Item::class)->find($id);
                $item->setTitre($formData->getTitre());
                $item->setContenu($formData->getContenu());
                $em->flush();

                return $this->redirectToRoute('item_all');
            }

        return $this->render('item/modify.html.twig' , [
            'data'=>$itemData,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/item/delete/{id}', name: 'item_delete')]
    public function itemDelete($id) : Response {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Item::class)->find($id);
        $em->remove($item);
        $em->flush();


        return $this->redirectToRoute('item_all');
    }
    #[Route('/item/single/{slug}', name: 'item_single')]
    public function itemSingle($slug) : Response{
        $repo =$this->getDoctrine()->getRepository(Item::class);

        $data = $repo->findOneBy(['slug' => $slug]);


        return $this->render('item/single.html.twig',[
            'data'=>$data
        ]);
    }

}
