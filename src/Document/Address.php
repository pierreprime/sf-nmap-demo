<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     description="Generic address description",
 *     title="Address"
 * )
 * @MongoDB\EmbeddedDocument
 */
class Address implements \JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @OA\Property(
     *     description="IPv4, IPv6 or MAC address",
     *     title="Address"
     * )
     * @MongoDB\Field(type="string")
     */
    private $address;

    /**
     * @OA\Property(
     *     description="IPv4, IPv6 or MAC enum",
     *     title="Address type"
     * )
     * @MongoDB\Field(type="string")
     */
    private $type;

    /**
     * @OA\Property(
     *     description="Vendor of address",
     *     title="vendor"
     * )
     * @MongoDB\Field(type="string")
     */
    private $vendor;

    public function __construct($address, $type, $vendor)
    {
        $this->address = $address;
        $this->type = $type;
        $this->vendor = $vendor;
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
}
