<?php

namespace App\Entity;

use App\Repository\PdvLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PdvLogRepository::class)
 */
class PdvLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Pdv::class, inversedBy="pdvLogs")
     */
    private $pdv;

    /**
     * @ORM\Column(type="array")
     */
    private $datas = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPdv(): ?Pdv
    {
        return $this->pdv;
    }

    public function setPdv(?Pdv $pdv): self
    {
        $this->pdv = $pdv;

        return $this;
    }

    public function getDatas(): ?array
    {
        return $this->datas;
    }

    public function setDatas(array $datas): self
    {
        $this->datas = $datas;

        return $this;
    }
}
