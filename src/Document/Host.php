<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     description="Host description, nmap report root",
 *     title="Host"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\HostRepository")
 */
class Host
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @OA\Property(
     *     description="Host state, UP or DOWN enum",
     *     title="State"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Address", mappedBy="host")
     */
    private $addresses;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Hostname", inversedBy="hosts")
     */
    private $hostnames;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Port", inversedBy="hosts")
     */
    private $ports;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->hostnames = new ArrayCollection();
        $this->ports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setHost($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getHost() === $this) {
                $address->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Hostname[]
     */
    public function getHostnames(): Collection
    {
        return $this->hostnames;
    }

    public function addHostname(Hostname $hostname): self
    {
        if (!$this->hostnames->contains($hostname)) {
            $this->hostnames[] = $hostname;
        }

        return $this;
    }

    public function removeHostname(Hostname $hostname): self
    {
        if ($this->hostnames->contains($hostname)) {
            $this->hostnames->removeElement($hostname);
        }

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
        }

        return $this;
    }

    public function removePort(Port $port): self
    {
        if ($this->ports->contains($port)) {
            $this->ports->removeElement($port);
        }

        return $this;
    }
}
