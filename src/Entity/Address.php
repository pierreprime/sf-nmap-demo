<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @OA\Schema(
 *     description="Generic address description",
 *     title="Address"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @OA\Property(
     *     description="IPv4, IPv6 or MAC address",
     *     title="Address"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @OA\Property(
     *     description="IPv4, IPv6 or MAC enum",
     *     title="Address type"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @OA\Property(
     *     description="Vendor of address",
     *     title="vendor"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vendor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Host", inversedBy="addresses")
     */
    private $host;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getHost(): ?Host
    {
        return $this->host;
    }

    public function setHost(?Host $host): self
    {
        $this->host = $host;

        return $this;
    }
}
