<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(): Response
    {
 
        // https://gist.github.com/thagxt/d9b4388156aeb7f1d66b108d728470d2 CURLOPT_RETURNTRANSFER
        
        die;
        /* Open the Zip file */
        $zip = new \ZipArchive;
        $extractPath = $extractDir;

        if($zip->open($zipFile) != "true"){
            echo "Error :- Unable to open the Zip File";
        } 

        /* Extract Zip File */
        $zip->extractTo($extractPath);
        $zip->close();
        

        
        die;
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
