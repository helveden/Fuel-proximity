<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadPDVCommand extends Command
{
    protected static $defaultName = 'UploadPDV';
    protected static $defaultDescription = 'Command for upload and extract zip in app !';

    protected function configure(): void
    {
        $this
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Command for upload and extract zip in app !')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('You upload file !');
        $url = $this->getContainer()->getParameter('url_fuel_url_zip'); // URL of what you wan to download
        $zipFile = "PrixCarburants_instantane.zip"; // Rename .zip file
        $extractDir = $this->getContainer()->getParameter('url_pdv_list'); // Name of the directory where files are extracted
        $zipResource = fopen($zipFile, "w");

        // Get The Zip File From Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_FILE, $zipResource);

        $page = curl_exec($ch);

        if(!$page) {
            echo "Error :- ".curl_error($ch);
        }

        curl_close($ch);

        $io->success('File is upload !');

        $io->success('File is extract !');

        return Command::SUCCESS;
    }
}
