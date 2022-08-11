<?php

namespace App\Entity;

use App\Repository\PdvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PdvRepository::class)
 */
class Pdv
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $pdv_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longitude;

    /**
     * @ORM\Column(type="array")
     */
    private $datas = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postalcode;

    /**
     * @ORM\OneToMany(targetEntity=PdvLog::class, mappedBy="pdv")
     */
    private $pdvLogs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    public function __construct()
    {
        $this->pdvLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPdvId(): ?int
    {
        return $this->pdv_id;
    }

    public function setPdvId(int $pdv_id): self
    {
        $this->pdv_id = $pdv_id;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

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

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * @return Collection<int, PdvLog>
     */
    public function getPdvLogs(): Collection
    {
        return $this->pdvLogs;
    }

    public function addPdvLog(PdvLog $pdvLog): self
    {
        if (!$this->pdvLogs->contains($pdvLog)) {
            $this->pdvLogs[] = $pdvLog;
            $pdvLog->setPdv($this);
        }

        return $this;
    }

    public function removePdvLog(PdvLog $pdvLog): self
    {
        if ($this->pdvLogs->removeElement($pdvLog)) {
            // set the owning side to null (unless already changed)
            if ($pdvLog->getPdv() === $this) {
                $pdvLog->setPdv(null);
            }
        }

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
