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
 * @MongoDB\Document
 */
class Host implements \JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @OA\Property(
     *     description="Host state, UP or DOWN enum",
     *     title="State"
     * )
     * @MongoDB\Field(type="string")
     */
    private $state;

    /**
     * @MongoDB\EmbedMany(targetDocument="Address")
     */
    private $addresses;

    /**
     * @MongoDB\EmbedMany(targetDocument="Hostname")
     */
    private $hostnames;

    /**
     * @MongoDB\EmbedMany(targetDocument="Port")
     */
    private $ports;

    public function __construct($state, $addresses, $hostnames, $ports)
    {
        unset($this->addresses);
        $this->state = $state;
        $this->addresses = $addresses;
        $this->hostnames = $hostnames;
        $this->ports = $ports;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    public function getId(): ?string
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
     * @return Address[]
     */
    public function getAddresses()
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

    public function __toString()
    {
        return 'toto';
    }
}
