<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    
    /**
     * @Route("/get-pdv", name="app_get_pdv")
     */
    public function getPdv(Request $req)
    {
        $city = json_decode($req->getContent(), true);
        $city = str_replace(' ', '+', strtolower($city['city']));
        $req = sprintf($this->getParameter('api_address'), $city);
        
        // resultat de l'appel api gouv
        
        return $this->json(json_decode(file_get_contents($req), true));
    }
}
