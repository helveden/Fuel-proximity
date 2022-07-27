<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\Service\PdvFactory;

class UploadPDVCommand extends Command
{
    protected static $defaultName = 'UploadPDV';
    protected static $defaultDescription = 'Command for upload and extract zip in app !';

    protected $container;

    public function __construct(ContainerInterface $container, PdvFactory $pdvfacto) {
        $this->container = $container;
        $this->pdvfacto = $pdvfacto;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Command for upload and extract zip in app !')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        $io = new SymfonyStyle($input, $output);
        
        $io->section('You upload file !');

        $url = $this->container->getParameter('url_fuel_url_zip'); // URL of what you wan to download
        $zipFile = "PrixCarburants_instantane.zip"; // Rename .zip file
        $extractDir = $this->container->getParameter('dir_pdv_list_path'); // Name of the directory where files are extracted
        $zipResource = fopen($zipFile, "w");

        $xmlFile = $this->container->getParameter('path_xml_file');

        $pathFile = $extractDir.$zipFile;

        // Get The Zip File From Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);

        $page = curl_exec($ch);
        
        if(!$page) {
            echo "Error :- ".curl_error($ch);
        }

        curl_close($ch);

        $io->success('File is upload !');

        $io->section('File start copy !');        
        rename($zipFile, $pathFile);
        $io->success('File end copy !');

        $io->success('File start extract !');
        /* Open the Zip file */
        $zip = new \ZipArchive;

        if($zip->open($pathFile) != "true"){
            $io->warning('Error :- Unable to open the Zip File');
            die();
        } 

        /* Extract Zip File */
        $zip->extractTo($extractDir);
        $zip->close();
        $io->success('File end extract !');

        unlink($pathFile);
        $io->success('File delete !');

        // XML
        $contentXML = simplexml_load_file($xmlFile);
        $pdvs = [];
        foreach($contentXML as $pdv) {
            $pdvs[] = json_encode($pdv);
        }

        $io->caution('Count pdvs ' . count($pdvs));

        $this->pdvfacto->saveCommandAll($pdvs, $io);

        $io->success('End save !');

        return Command::SUCCESS;
    }
}
