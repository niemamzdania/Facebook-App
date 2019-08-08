<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Form\EditQuestFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    
    /**
     * @Route("/", name="test")
     */
    //public function index(Request $request)
    //{
    //    $quest = new Quests();

    //    $form = $this->createForm(EditQuestFormType::class, $quest);

     //   $form->handleRequest($request);

       // if ($form->isSubmitted() && $form->isValid()) {
        //    dd($quest);
       // }

      //  return $this->render('index.html.twig', ['form' => $form->createView()]);
    //}


    /**
     * @Route("/main", name="main")
     */
    public function index1()
    {
        

        return $this->render('main.html.twig');
    }


}
