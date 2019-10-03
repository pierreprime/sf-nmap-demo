<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Annotations as OA;

/**
 * @MongoDB\EmbeddedDocument
 */
class Service implements \JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @MongoDB\Field(type="string")
     */
    private $product;

    /**
     * @MongoDB\Field(type="string")
     */
    private $version;

    public function __construct($name, $product, $version)
    {
        $this->name = $name;
        $this->product = $product;
        $this->version = $version;
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
}
