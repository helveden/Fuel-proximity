<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Pdv;
use App\Entity\User;

class PdvFactory extends AbstractFactory {

    private $em;

    public function __construct(
        EntityManagerInterface $em,
        Security $security
    )
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function get(int $id) {
        return $this->em->getRepository(Pdv::class)->find($id);
    }

    public function getBy($params = []) {
        return $this->em->getRepository(Pdv::class)->findOneBy($params);
    }
    
    public function filter(array $params = []) {

    }

    public function saveCommandAll(array $pdvs = [], $io) {
        $io->progressStart();

        foreach ($io->progressIterate($pdvs) as $k => $pdv) {
            $this->save($pdv);
        }

        $io->progressFinish();
    }

    public function save($pdv) {
        $datas = [];

        $pdvDatas = json_decode($pdv, true);
        // savoir si le pdv existe
        $pdv = $this->getBy([
            'pdv_id' => $pdvDatas['@attributes']['id']
        ]);
        
        if($pdv === null):
            $pdv = new Pdv();
            $pdv->setPdvId($pdvDatas['@attributes']['id']);
            $pdv->setLatitude($pdvDatas['@attributes']['latitude']);
            $pdv->setLongitude($pdvDatas['@attributes']['longitude']);
            $pdv->setPostalcode($pdvDatas['@attributes']['cp']);
        endif;

        $pdv->setDatas($pdvDatas);

        $this->em->persist($pdv);
        $this->em->flush();

        return $datas;
    }
}