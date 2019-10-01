<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Port", mappedBy="service")
     */
    private $ports;

    public function __construct()
    {
        $this->ports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(?string $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection|Port[]
     */
    public function getPorts(): Collection
    {
        return $this->ports;
    }

    public function addPort(Port $port): self
    {
        if (!$this->ports->contains($port)) {
            $this->ports[] = $port;
            $port->setService($this);
        }

        return $this;
    }

    public function removePort(Port $port): self
    {
        if ($this->ports->contains($port)) {
            $this->ports->removeElement($port);
            // set the owning side to null (unless already changed)
            if ($port->getService() === $this) {
                $port->setService(null);
            }
        }

        return $this;
    }
}
