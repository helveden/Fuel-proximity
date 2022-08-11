<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Pdv;

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
     * @Route("/get-cities", name="app_get_cities")
     */
    public function getCities(Request $req)
    {
        $city = json_decode($req->getContent(), true);
        $city = str_replace(' ', '+', strtolower($city['city']));
        $req = sprintf($this->getParameter('api_address'), $city);
        
        // resultat de l'appel api gouv
        
        return $this->json(json_decode(file_get_contents($req), true));
    }
    
    /**
     * @Route("/get-pdvs", name="app_get_pdvs")
     */
    public function getPdvs(Request $req, ManagerRegistry $doctrine)
    {        
        $context = json_decode($req->getContent(), true);
        $context = $context['context'];

        $postalcode = substr($context, 0, 2);
        $postalcode .= '%';
        
        $pdvs = $doctrine->getRepository(Pdv::class)->findByPostcode($postalcode);
        
        return $this->json($pdvs);
    }
}
